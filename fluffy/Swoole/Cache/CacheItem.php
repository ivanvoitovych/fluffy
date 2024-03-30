<?php

namespace Fluffy\Swoole\Cache;

class CacheItem
{
    public function __construct(public int $syncKey, public $data)
    {
    }
}
