<?php

namespace Fluffy\Swoole\Viewi;

use Fluffy\Domain\Message\FpmHttpResponse;
use Fluffy\Domain\Message\HttpContext;
use Fluffy\Middleware\RoutingMiddleware;
use Fluffy\ViewiIntegration\Message\ViewiHttpRequest;
use Fluffy\ViewiIntegration\Message\ViewiHttpResponse;
use DotDi\DependencyInjection\IServiceProvider;
use Viewi\Routing\RouteAdapterBase;
use Viewi\Routing\Router;

class SwooleViewiAdapter extends RouteAdapterBase
{
    public function __construct(private IServiceProvider $serviceProvider)
    {
    }

    public function register($method, $url, $component, $defaults)
    {
        // skip
    }

    public function handle($method, $url, $params = null)
    {
        // create scope
        $scope = $this->serviceProvider->createScope();
        // create HttpContext (HttpRequest, HttpResponse)
        $httpResponse = new ViewiHttpResponse();
        $httpContext = new HttpContext(
            new ViewiHttpRequest(
                $method,
                $url,
                [], // TODO: copy headers from parent request
                $params ?? []
            ),
            $httpResponse
        );
        // register local context
        $scope->serviceProvider->set(HttpRequest::class, $httpContext->request);
        $scope->serviceProvider->set(HttpResponse::class, $httpContext->response);
        $scope->serviceProvider->set(HttpContext::class, $httpContext);
        // invoke
        $routingMiddleware = new RoutingMiddleware($httpContext, $scope);
        $routingMiddleware->invoke();
        // dispose
        $scope->dispose();
        unset($routingMiddleware);
        unset($scope);
        return $httpResponse->getRawResponseData();
    }
}
