<?php

namespace Fluffy\Controllers;

class BaseController
{
    public function Unauthorized(?string $message = null)
    {
        return Response::Json([
            "message" => $message ?? "Unauthorized"
        ])->WithCode(401);
    }

    public function Forbidden(?string $message = null)
    {
        return Response::Json([
            "message" => $message ?? "Forbidden"
        ])->WithCode(403);
    }

    public function NotFound()
    {
        return Response::Json([
            "message" => "Not Found"
        ])->WithCode(404);
    }

    public function Conflict()
    {
        return Response::Json([
            "message" => "Not Found"
        ])->WithCode(409);
    }

    public function BadRequest($errors = null)
    {
        return Response::Json([
            "message" => "Bad Request",
            "errors" => $errors
        ])->WithCode(400);
    }

    public function TooManyRequests($errors = null)
    {
        return Response::Json([
            "message" => "Too Many Requests",
            "errors" => $errors
        ])->WithCode(429);
    }

    public function ServerError($errors = null)
    {
        return Response::Json([
            "message" => "Server Error",
            "errors" => $errors
        ])->WithCode(500);
    }

    public function File($content, $mimeType = null)
    {
        return Response::File($content, $mimeType);
    }

    public function Redirect(string $location, int $code = 302)
    {
        return (new Response())
            ->WithHeaders(['Location' => $location])
            ->WithCode($code);
    }
}
