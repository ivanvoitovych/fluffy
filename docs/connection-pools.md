# Connection Pools — Fluffy Framework

How Fluffy pools PostgreSQL, Redis and ClickHouse connections, how to size them, and the failure
mode that sizing gets wrong. Everything here lives in `fluffy/Swoole/Database/` and is wired in
`Domain/App/BaseStartUp.php`.

---

## 1. One pool per worker, not per app

Every Swoole worker process holds its **own** pool — request workers *and* task workers. There is
no cross-process sharing, so the app's real connection ceiling is:

```
poolSize x (worker_num + task_worker_num)
```

With the usual one-worker-per-core sizing on a 4-core box that is `poolSize x 8`. A blue/green
rotation briefly runs **two** full instances, so budget double that against the server's own limit.

Concretely, at `poolSize: 8` on a 4-core box: 64 connections per colour, 128 across a rotation.
Against PostgreSQL's default `max_connections` of 100 (3 reserved for superusers) that does not
fit — which is why `poolSize` and `max_connections` are a single decision, never two. Swoole's own
default of `DEFAULT_SIZE = 64` is wildly wrong for this shape and must never be left in place.

Idle steady state is roughly one connection per worker. Pools **never shrink**, so a burst pins the
high-water mark until the process restarts.

## 2. The wait must be bounded

`Swoole\ConnectionPool::get()` defaults to `pop(-1)` — an **unbounded** wait. This is the single
most dangerous default in the pooling layer, because of how the failure presents.

When a pool drains, every later caller *on that worker* blocks forever. The worker stops answering
entirely. nginx gives up at `proxy_read_timeout` (60s by default) and returns 504. Nothing is
logged anywhere, because nothing failed — it is still waiting.

With one wedged worker out of four the site is not down. It is **"slow"**: three quarters of
requests are served in milliseconds and a quarter hang for a minute. It stays that way until
someone restarts the process.

`WaitsForConnection` bounds it. Default **5s**, configurable per pool via `poolWaitSeconds` in the
connection's config block, `0` restores the old wait-forever:

```php
class MyPool extends ConnectionPool { use WaitsForConnection; }
```

The trait overrides `get()` on the pool rather than fixing call sites, so callers keep writing
`$pool->get()` and inherit the guard. On exhaustion it throws `PoolTimeoutException`, naming the
pool and its counters — deliberately not the database, because the server is usually healthy and
the pool is simply drained.

A bounded wait **cannot prevent a leak**. It makes one loud. Waiting seconds for a connection is
already pathological when queries run in milliseconds, so the timeout expiring always means
something worth a stack trace.

> **If you add a pool, add the trait.** `BaseStartUp` applies it to the PostgreSQL, Redis cache and
> ClickHouse pools, so a stock app is covered. An app that defines its own pool subclass — as
> Urlicer does with `RedisDbPool` — must `use WaitsForConnection;` itself or it silently keeps the
> unbounded default.

## 3. Returning a connection: `put()` vs `release()`

`ConnectionPool::put(null)` — the documented way to report a dead connection — decrements the
counter and immediately calls `make()`. That is the wrong moment to open a connection: the caller
is discarding one precisely because the server misbehaved, every worker tends to reach that point
in the same instant, and `make()` throws when the server is unreachable, surfacing the failure
during request disposal.

`ReleasesPoolSlots::release()` frees the slot **without** reconnecting. The pool sits one under its
size and the next `get()` opens a connection when something actually needs one. Use `release()` for
a connection you are throwing away, `put($conn)` for one you are handing back.

Order matters when discarding: close the dead connection **before** freeing its slot, so the
process never holds two of the server's connection slots for one pool entry.

## 4. The failure signature

Worth recognising, because it is invisible to the obvious check.

On 2026-09-02 a production Fluffy app answered roughly half its requests for six hours. The daily
apt upgrade had restarted PostgreSQL and the app in the same second; the app came up against a
database still restarting, pools drained, and request workers wedged on the unbounded `get()`.

**Sequential probes could not see it.** `curl` in a loop returned 200 in ~140ms every time, because
nginx's `keepalive` pool reuses a warm upstream connection pinned to a healthy worker. Only
*concurrent* requests opened new connections that landed on wedged workers:

```bash
for i in $(seq 1 10); do (curl -s -o /dev/null -w "%{http_code} %{time_total}\n" https://host/ --max-time 65) & done; wait
# healthy: 10x 200 in ~150ms.  wedged: a few 200s, the rest 504 at exactly 60s.
```

Two more discriminators:

- **Compare against a route that touches no pool.** In Urlicer the short-domain redirect
  short-circuits in middleware before routing; it stayed 100% healthy while every routed endpoint
  hung. "Redirects fine, app hanging" localises the fault to the pipeline, not the box or nginx.
- **Count server-side connections.** `select datname, state, count(*) from pg_stat_activity where
  backend_type='client backend' group by datname, state;` — compare against the §1 ceiling. Note
  the database name usually differs between local and prod, so drop the `datname` filter rather
  than trusting an empty result.

Every worker process sitting at 0% CPU while requests hang is the tell that this is a *blocked*
wait, not a busy one.
