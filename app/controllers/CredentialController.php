<?php

namespace App\controllers;

use App\models\Credential;
use AuthServerJwt\OAuthSrv;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class CredentialController
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $ok = false;
        $contents = $request->getParsedBody();

        $oAuthJWT = new OAuthSrv($contents['client'], dirname(__FILE__, 2) . $_ENV['PATH_TO_CERT'], $_ENV['CERT_SECRET']);

        $credentials = $oAuthJWT->genCredentials($_ENV['CERT_SECRET']);

        $dbCredentials = new Credential();

        $arrayAssoc = [
            'clientid' => $credentials['client_id'],
            'timestamp' => $credentials['credential_time']
        ];

        if (count($dbCredentials->findBy('username', $contents['client'])) > 0) {
            throw new HttpBadRequestException($request);
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

    public function getClient(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cred = [];

        $credentialsDB = new Credential();
        $credentials = $credentialsDB->findBy('username', $args['client'])[0];

        if (count($credentials) === 0) {
            throw new HttpNotFoundException($request);
        }

        $credentialPlainText = $credentials['username'] . '#' . $credentials['clientid'] . '#' . (string)$credentials['timestamp'] . '%' . $_ENV['CERT_SECRET'];
        $clientSecret = base64_encode(password_hash($credentialPlainText, PASSWORD_BCRYPT));

        $cred['client'] = $credentials['username'];
        $cred['time_cred'] = date(DATE_ATOM, $credentials['timestamp']);
        $cred['clientid'] = $credentials['clientid'];
        $cred['client_secret'] = $clientSecret;

        $response->getBody()->write(json_encode($cred));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
