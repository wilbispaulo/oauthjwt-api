<?php

namespace App\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ClientKeyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contents = $request->getParsedBody();

        // CLIENT KEY VALIDATION
        if (!array_key_exists('client', $contents) || ($contents['client'] === '')) {
            throw new HttpBadRequestException($request);
        }
        return $handler->handle($request);
    }
}
