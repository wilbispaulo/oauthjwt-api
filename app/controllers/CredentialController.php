<?php

namespace App\controllers;

use App\models\Credential;
use AuthServerJwt\OAuthSrv;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

class CredentialController
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $ok = false;
        $contents = $request->getParsedBody();

        $oAuthJWT = new OAuthSrv($contents['client'], dirname(__FILE__, 2) . $_ENV['PATH_TO_CERT'], $_ENV['CERT_SECRET']);

        $credentials = $oAuthJWT->genCredentials();

        $dbCredentials = new Credential();

        $arrayAssoc = [
            'clientid' => $credentials['client_id'],
            'timestamp' => $credentials['credential_time']
        ];

        if (count($dbCredentials->findBy('username', $contents['client'])) > 0) {
            $ok = $dbCredentials->update($arrayAssoc, 'username', $contents['client']);
        } else {
            $arrayAssoc['username'] = $contents['client'];
            $ok = $dbCredentials->create($arrayAssoc);
        }

        if (!$ok) {
            throw new HttpInternalServerErrorException($request);
        }

        $response->getBody()->write(json_encode($credentials));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
