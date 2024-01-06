<?php

namespace Fluffy\Domain\Hubs;

class Hubs
{
    /**
     * 
     * @var HubRouteItem[]
     */
    private static array $hubs = [];

    public static function mapHub(string $route, $action, ...$params)
    {
        self::$hubs[$route] = new HubRouteItem($route, $action, $params);
    }

    public static function resolve(string $route): ?HubRouteItem
    {
        if (isset(self::$hubs[$route])) {
            return self::$hubs[$route];
        }
        return null;
    }
}
