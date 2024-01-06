<?php

namespace Fluffy\Domain\Message;

class FpmHttpRequest extends HttpRequest
{
    public function __construct(
        string $method,
        string $uri,
        array $headers = array(),
        array $query = array()
    ) {
        parent::__construct($method, $uri, $headers, $query);
    }

    public function getCookie(?string $name = null) { }

    public function getHeader(?string $name = null) { }

    public function getBody()
    {
        return file_get_contents('php://input');
    }
}
