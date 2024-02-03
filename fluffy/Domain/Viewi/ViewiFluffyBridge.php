<?php

namespace Fluffy\Domain\Viewi;

use DotDi\DependencyInjection\Container;
use DotDi\DependencyInjection\IServiceProvider;
use Fluffy\Domain\Message\HttpContext;
use Fluffy\Domain\Message\HttpRequest;
use Fluffy\Domain\Message\HttpResponse;
use Fluffy\Middleware\RoutingMiddleware;
use Viewi\Bridge\DefaultBridge;
use Viewi\Components\Http\Message\Request;
use Viewi\Engine;

class ViewiFluffyBridge extends DefaultBridge
{
    public function __construct(private IServiceProvider $serviceProvider)
    {
    }

    public function request(Request $request, Engine $currentEngine): mixed
    {
        if ($request->isExternal) {
            return parent::externalRequest($request); // TODO: use Swoole http client
        }
        /**
         * @var Container $currentScope
         */
        $currentScope = $currentEngine->getIfExists(Container::class);
        /**
         * @var HttpContext $currentHttpContext
         */
        $currentHttpContext = $currentScope->serviceProvider->get(HttpContext::class);
        $cookies = $currentHttpContext->request->getCookie();
        $headers = $currentHttpContext->request->getHeader();
        $parts = parse_url($request->url);
        $newRequest = new ViewiHttpRequest(
            $request->method,
            $parts['path'],
            $request->headers,
            $parts['query'] ?? []
        );
        $newRequest->cookie = $cookies; // copy cookie from parent
        $newRequest->header = $headers; // copy headers from parent
        $scope = $this->serviceProvider->createScope();
        try {
            // create request and http context
            $httpResponse = new ViewiHttpResponse();
            $httpContext = new HttpContext(
                $newRequest,
                $httpResponse
            );

            $scope->serviceProvider->set(HttpRequest::class, $httpContext->request);
            $scope->serviceProvider->set(HttpResponse::class, $httpContext->response);
            $scope->serviceProvider->set(HttpContext::class, $httpContext);
            // invoke
            $routingMiddleware = new RoutingMiddleware($httpContext, $scope);
            $routingMiddleware->invoke();
            return $httpResponse->rawData;
        } finally {
            // dispose scope
            // unset($requestDelegate);
            $scope->dispose();
            // unset($scope);
        }
    }
}
