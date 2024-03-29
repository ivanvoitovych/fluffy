<?php

namespace Fluffy\Swoole\Connectors;

use DotDi\Interfaces\IDisposable;
use Redis;
use Swoole\Database\RedisPool;

class SwooleRedisConnector implements IDisposable
{
    private Redis $redis;

    public function __construct(private RedisPool $pool)
    {
    }

    /**
     * 
     * @return Redis 
     */
    function get()
    {
        if (!isset($this->redis)) {
            $this->redis = $this->pool->get();
        }
        return $this->redis;
    }

    function dispose()
    {
        if (isset($this->redis)) {
            $this->pool->put($this->redis);
            unset($this->redis);
        }
    }
}
