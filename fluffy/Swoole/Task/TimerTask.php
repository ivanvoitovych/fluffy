<?php

namespace Fluffy\Swoole\Task;

class TimerTask
{
    public function __construct(public string $key, public int $expireAtSec)
    {
    }
}
