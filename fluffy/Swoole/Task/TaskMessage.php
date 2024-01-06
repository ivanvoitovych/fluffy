<?php

namespace Fluffy\Swoole\Task;

class TaskMessage
{
    public int $workerId;
    public function __construct(public string $dispatcherUID, public $data = null)
    {
    }
}
