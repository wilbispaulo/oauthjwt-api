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

        $oAuthJWT = new OAuthSrv($contents['client_aud'], dirname(__FILE__, 3) . $_ENV['PATH_TO_CERT'], $_ENV['CERT_SECRET']);

        $token = $oAuthJWT->tokenJWT($_ENV['ISSUER'], $_ENV['EXP_TOKEN'], $contents['scope']);

        $dataHoraExp = date(DATE_ATOM, $oAuthJWT->getClaims()['exp']);

        $response->getBody()->write(json_encode([
            'client' => $oAuthJWT->getClaims()['aud'],
            'token_expiration' => $dataHoraExp,
            'scope' => $oAuthJWT->getClaims()['scope'],
            'token' => $token
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
