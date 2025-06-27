<?php

namespace App\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;

class BodyBasicParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contents = $request->getParsedBody();

        $ok = is_array($contents);

        if ($ok) {
            $ok = array_key_exists('clientid', $contents) && array_key_exists('claims', $contents);
        }

        if ($ok) {
            $ok = is_array($contents['claims']);
            // Valida uuid com regex
            $regex = "/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/";
            $ok = (preg_match($regex, $contents['clientid']) === 1) ? true : false;
        }

        if ($ok === false) {
            throw new HttpBadRequestException($request);
        }

        return $handler->handle($request);
    }
}
