<?php

namespace Fluffy\Swoole\Database;

/**
 * Bounds the wait for a pooled connection, and turns exhaustion into an exception.
 *
 * Swoole's `ConnectionPool::get()` defaults to `pop(-1)` — an UNBOUNDED wait. That default is the
 * difference between a loud failure and a silent one. When a pool drains (connections borrowed and
 * never returned), every later caller ON THAT WORKER blocks forever: the worker stops answering
 * entirely, nginx gives up at `proxy_read_timeout` (60s by default) and returns 504, and nothing
 * anywhere logs a cause. With one wedged worker out of four the site is not down — it is "slow",
 * failing a quarter of requests, and it stays that way until someone restarts it.
 *
 * That is exactly what happened on 2026-09-02: the daily apt upgrade restarted PostgreSQL and the
 * app in the same second, the app came up against a database that was still restarting, and workers
 * wedged for six hours with no error in the journal.
 *
 * A bounded wait cannot prevent a leak — it makes one LOUD. Waiting seconds for a connection is
 * already pathological here (queries run in milliseconds), so the timeout expiring always means
 * something worth a stack trace. Set the wait to 0 to opt back into waiting forever.
 *
 * `$pool`, `$num` and `$size` are protected on Swoole\ConnectionPool, so this only reads its own
 * state — same trick as ReleasesPoolSlots.
 */
trait WaitsForConnection
{
    /**
     * Seconds to wait for a free connection before giving up. Generous on purpose: this is a
     * dead-man's switch, not a load shedder, and it should never fire on a healthy pool.
     */
    protected float $waitSeconds = 5.0;

    /**
     * Override the default wait. 0 (or negative) restores Swoole's wait-forever behaviour.
     */
    public function withWaitTimeout(float $seconds): static
    {
        $this->waitSeconds = $seconds;
        return $this;
    }

    /**
     * @param float $timeout Seconds. -1 (Swoole's default, and what every call site passes by
     *                       omitting it) means "use the configured wait".
     * @throws PoolTimeoutException When the pool is closed, or drained for longer than the wait.
     */
    public function get(float $timeout = -1)
    {
        if ($this->pool === null) {
            throw new PoolTimeoutException(static::class . ': connection pool is closed.');
        }
        $wait = $timeout >= 0 ? $timeout : $this->waitSeconds;
        if ($wait <= 0) {
            // Explicitly configured to wait forever. Channel::pop(0) is ambiguous across Swoole
            // versions, so route this through -1 rather than passing a bare 0 down.
            return parent::get(-1);
        }
        $connection = parent::get($wait);
        if ($connection === false) {
            throw new PoolTimeoutException(sprintf(
                '%s: no free connection after %.1fs (pool size %d, %d opened). The pool is drained — '
                    . 'connections are being borrowed and not returned.',
                static::class,
                $wait,
                $this->size,
                $this->num
            ));
        }
        return $connection;
    }
}
