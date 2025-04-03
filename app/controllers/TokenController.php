<?php

namespace App\controllers;

use AuthServerJwt\OAuthSrv;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TokenController
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contents = $request->getParsedBody();


        $oAuthJWT = new OAuthSrv($contents['client'], dirname(__FILE__, 3) . $_ENV['PATH_TO_CERT'], $_ENV['CERT_SECRET']);

        $token = $oAuthJWT->tokenJWT($_ENV['ISSUER'], $_ENV['EXP_TOKEN'], $contents['username'], $contents['password'], $contents['scope']);

        $tokenExp = "";

        $response->getBody()->write(json_encode([
            'client' => $contents['client'],
            'token_expiration' => $tokenExp,
            'token' => $token
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
