<?php

namespace Fluffy\Domain\Message;

class HttpContext
{
    public function __construct(public HttpRequest $request, public HttpResponse $response)
    {
    }
}
