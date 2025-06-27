<?php

namespace App\middlewares;

use App\models\Credential;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;

class ClientIdDBValidationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contents = $request->getParsedBody();

        $credentialsDB = new Credential();
        if (count($credentialsDB->findBy('clientid', $contents['clientid'])) === 0) {
            throw new HttpBadRequestException($request);
        }

        return $handler->handle($request);
    }
}
