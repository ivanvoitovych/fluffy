<?php

namespace Fluffy\Domain\Message;

class SocketMessage
{
    public function __construct(public string $route, public $data)
    {
    }
}
