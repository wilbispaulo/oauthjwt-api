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

        $oAuthJWT = new OAuthSrv($contents['client'], dirname(__FILE__, 3) . $_ENV['PATH_TO_CERT'], $_ENV['CERT_SECRET']);

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
        $credentials = $credentialsDB->findBy('username', $args['client']);

        if (count($credentials) === 0) {
            throw new HttpNotFoundException($request);
        }

        $credentialPlainText = $credentials[0]['username'] . '#' . $credentials[0]['clientid'] . '#' . (string)$credentials[0]['timestamp'] . '%' . $_ENV['CERT_SECRET'];
        $clientSecret = base64_encode(password_hash($credentialPlainText, PASSWORD_BCRYPT));

        $cred['client'] = $credentials[0]['username'];
        $cred['time_cred'] = date(DATE_ATOM, $credentials[0]['timestamp']);
        $cred['clientid'] = $credentials[0]['clientid'];
        $cred['client_secret'] = $clientSecret;

        $response->getBody()->write(json_encode($cred));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cred = [];

        $credentialsDB = new Credential();
        $credentials = $credentialsDB->fetchAll();

        if (count($credentials) === 0) {
            throw new HttpNotFoundException($request);
        }

        $i = 0;
        foreach ($credentials as $credential) {
            $credentialPlainText = $credential['username'] . '#' . $credential['clientid'] . '#' . (string)$credential['timestamp'] . '%' . $_ENV['CERT_SECRET'];
            $clientSecret = base64_encode(password_hash($credentialPlainText, PASSWORD_BCRYPT));
            $cred[$i]['client'] = $credential['username'];
            $cred[$i]['time_cred'] = date(DATE_ATOM, $credential['timestamp']);
            $cred[$i]['clientid'] = $credential['clientid'];
            $cred[$i]['client_secret'] = $clientSecret;
            $i++;
        }

        $response->getBody()->write(json_encode($cred, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
