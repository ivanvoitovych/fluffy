<?php

namespace Fluffy\Domain\App;

use Fluffy\Domain\Middleware\IMiddleware;
use DotDi\DependencyInjection\Container;
use DotDi\DependencyInjection\ServiceProviderHelper;
use Exception;

class RequestDelegate
{
    private int $index = -1;

    function __construct(private BaseApp $app, private Container $scope)
    {
    }

    function __invoke()
    {
        $this->index++;
        if ($this->index < $this->app->middlewareCount) {
            $middleware = $this->app->middleware[$this->index];
            $dependencies = ServiceProviderHelper::getDependencies($middleware, $this->scope->serviceProvider);
            if (is_string($middleware)) // middleware class
            {
                if (!empty($dependencies)) {
                    $middlewareInstance = new $middleware(...$dependencies);
                } else {
                    $middlewareInstance = new $middleware();
                }
                if ($middlewareInstance instanceof IMiddleware) {
                    $middlewareInstance->invoke();
                } else {
                    throw new Exception("Unsupported Middleware. Type does not implement IMiddleware interface: " . print_r($middleware, true));
                }
            } else if (is_callable($middleware)) { // callable
                if (!empty($dependencies)) {
                    $middleware(...$dependencies);
                } else {
                    $middleware();
                }
            } else {
                throw new Exception("Unsupported Middleware " . print_r($middleware, true));
            }
        }
    }
}
