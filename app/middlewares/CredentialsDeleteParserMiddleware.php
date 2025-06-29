<?php

namespace App\middlewares;

use App\models\Credential;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class CredentialsDeleteParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();

        $credentials = new Credential();
        $data = $credentials->findBy('username', $args['client']);

        if ($data === false) {
            throw new HttpInternalServerErrorException($request);
        }
        if (count($data) === 0) {
            throw new HttpNotFoundException($request);
        }

        return $handler->handle($request->withParsedBody($data[0]));
    }
}
