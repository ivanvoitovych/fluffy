<?php

namespace Fluffy\Swoole\Message;

use Fluffy\Domain\Message\HttpResponse;

class SwooleHttpResponse extends HttpResponse
{
    public function __construct(private \Swoole\Http\Response $swooleResponse)
    {
    }

    public function setCookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain  = '', bool $secure = false, bool $httpOnly = false, string $sameSite = '', string $priority = ''): bool
    {
        return $this->swooleResponse->cookie($key, $value, $expire, $path, $domain, $secure, $httpOnly, $sameSite, $priority);
    }

    public function end()
    {
        $this->swooleResponse->status($this->status);
        foreach ($this->headers as $key => $value) {
            $this->swooleResponse->header($key, $value);
        }
        $this->swooleResponse->end($this->body);
    }
}
