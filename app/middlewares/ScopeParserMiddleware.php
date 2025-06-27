<?php

namespace App\middlewares;

use App\models\Endpoint;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpInternalServerErrorException;

class ScopeParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contents = $request->getParsedBody();

        $endpoints = new Endpoint();
        $claimsDB = $endpoints->findBy('clientid', $contents['username']);
        if (count($claimsDB) === 0) {
            throw new HttpInternalServerErrorException($request);
        }
        $claims = [];
        foreach ($claimsDB as $key => $record) {
            $claims[$key] = $record['endpoint'] . '/' . $record['method'];
        }

        $contents['scope'] = $claims;

        return $handler->handle($request->withParsedBody($contents));
    }
}
