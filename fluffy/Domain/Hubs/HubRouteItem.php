<?php

namespace Fluffy\Domain\Hubs;

class HubRouteItem
{
    public function __construct(public string $route, public $action, public array $params = [])
    {
    }
}
