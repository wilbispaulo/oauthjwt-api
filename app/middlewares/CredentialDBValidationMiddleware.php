<?php

namespace App\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CredentialDBValidationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $credentials = $request->getParsedBody();

        $credentials['client'] = 'APP.DEVENV.WWP';


        return $handler->handle($request->withParsedBody($credentials));
    }
}
