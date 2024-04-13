<?php

namespace Fluffy\Swoole\Message;

use Fluffy\Domain\Message\HttpRequest;

class SwooleHttpRequest extends HttpRequest
{
    public function __construct(
        private \Swoole\Http\Request $swooleRequest,
        string $method,
        string $uri,
        array $headers = array(),
        array $query = array(),
        array $server = array(),
    ) {
        parent::__construct($method, $uri, $headers, $query);
    }

    public function getCookie(?string $name = null): mixed
    {
        // var_dump($this->swooleRequest->cookie);
        return $name ? $this->swooleRequest->cookie[$name] ?? null : $this->swooleRequest->cookie;
    }

    public function getBody()
    {
        return $this->swooleRequest->getContent();
    }

    public function getHeader(?string $name = null)
    {
        // print_r($this->swooleRequest->header);
        return $name ? $this->swooleRequest->header[strtolower($name)] ?? null : $this->swooleRequest->header;
    }
}
