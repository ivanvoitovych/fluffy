<?php

namespace Fluffy\Domain\Message;

class ResponseBuilder
{
    public array $headers = [];
    public int $statusCode = 200;
    public string $statusText = '';
    public $body = '';
    public bool $stringify = false;

    static function json($data)
    {
        $response = new ResponseBuilder();
        $response->body = $data;
        $response->stringify = true;
        $response->headers['Content-type'] = 'application/json; charset=utf-8';
        return $response;
    }

    static function html($data)
    {
        $response = new ResponseBuilder();
        $response->body = $data;
        $response->headers['Content-type'] = 'text/html; charset=utf-8';
        return $response;
    }

    static function xml($data)
    {
        $response = new ResponseBuilder();
        $response->body = $data;
        $response->headers['Content-type'] = 'application/xml';
        return $response;
    }

    static function text($data)
    {
        $response = new ResponseBuilder();
        $response->body = $data;
        $response->headers['Content-type'] = 'text/plain; charset=utf-8';
        return $response;
    }

    static function file($data, $mimeType = null, $contentDisposition = null)
    {
        $response = new ResponseBuilder();
        $response->body = $data;
        if ($mimeType !== null) {
            $response->headers['Content-type'] = $mimeType;
        }
        if($contentDisposition !== null)
        {
            $response->headers['Content-Disposition'] = $contentDisposition;
        }
        return $response;
    }

    function withCode(int $code)
    {
        $this->statusCode = $code;
        return $this;
    }

    function withHeaders(array $headers)
    {
        $this->headers += $headers;
        return $this;
    }

    function withBody($data)
    {
        $this->body = $data;
        return $this;
    }

    function asJson()
    {
        $this->stringify = true;
        $this->headers['Content-type'] = 'application/json; charset=utf-8';
        return $this;
    }

    function asPlainText()
    {
        $this->stringify = false;
        $this->headers['Content-type'] = 'text/plain; charset=utf-8';
        return $this;
    }

    function asHtml()
    {
        $this->stringify = false;
        $this->headers['Content-type'] = 'text/html; charset=utf-8';
        return $this;
    }

    function withBodyType(string $bodyType)
    {
        $this->headers['Content-type'] = $bodyType;
        return $this;
    }
}