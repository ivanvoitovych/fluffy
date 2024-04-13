<?php

namespace Fluffy\Swoole\RateLimit;

class RateLimitService
{
    public function __construct(private \AppServer $appServer)
    {
    }

    public function limit(string $key, int $max, int $lifetime): bool
    {
        $final = $this->appServer->timeTable->incr($key, 'value');
        // print_r([$key, $final]);
        if ($final === 1) {
            // set up clean up timer
            \Swoole\Timer::after($lifetime * 1000, function () use ($key) {
                $this->appServer->timeTable->del($key);
            });
        }
        if ($final > $max) {
            // do not allow overflow
            $this->appServer->timeTable->set($key, ['value' => $max + 1]);
            return false;
        }
        return true;
    }
}
