<?php

namespace Fluffy\Swoole\Database;

use Swoole\Database\RedisPool;

/**
 * Swoole's RedisPool with a bounded wait (see WaitsForConnection).
 *
 * Exists only because BaseStartUp constructed `Swoole\Database\RedisPool` directly, leaving no
 * class of ours to hang the trait on. It is still registered in the container under
 * `RedisPool::class`, so RedisConnector and everything else keep type-hinting the Swoole class
 * and none of them need to know this subclass exists.
 */
class RedisCachePool extends RedisPool
{
    use WaitsForConnection;
}
