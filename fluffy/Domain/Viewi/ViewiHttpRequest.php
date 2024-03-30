<?php

namespace Fluffy\Domain\Viewi;

use Fluffy\Domain\Message\HttpRequest;

class ViewiHttpRequest extends HttpRequest
{
    public ?array $cookie = [];
    public ?array $header = [];

    public function getBody()
    {
        return '';
    }

    public function getCookie(?string $name = null)
    {
        return $name ? $this->cookie[$name] ?? null : $this->cookie;
    }

    public function getHeader(?string $name = null)
    {
        return $name ? $this->header[strtolower($name)] ?? null : $this->header;
    }
}
