<?php

namespace App\controllers;

use AuthServerJwt\OAuthSrv;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CredentialController
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contents = $request->getParsedBody();

        $oAuthJWT = new OAuthSrv($contents['client'], dirname(__FILE__, 2) . $_ENV['PATH_TO_CERT'], $_ENV['CERT_SECRET']);

        $credentials = $oAuthJWT->genCredentials();

        $response->getBody()->write(json_encode($credentials));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
