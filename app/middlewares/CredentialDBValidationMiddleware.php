<?php

namespace App\middlewares;

use App\models\Credential;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;

class CredentialDBValidationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $credentials = $request->getParsedBody();

        $userDB = new Credential();
        $user = $user = $userDB->findBy('clientid', $credentials['username']);
        if ($user === false) {
            throw new HttpInternalServerErrorException($request);
        }

        if (count($user) === 0) {
            throw new HttpUnauthorizedException($request);
        }

        $credential = base64_decode($credentials['password']);
        $credentialPlainText = $user[0]['username'] . '#' . $credentials['username'] . '#' . (string)$user[0]['timestamp'];


        if (!password_verify($credentialPlainText, $credential)) {
            throw new HttpUnauthorizedException($request);
        }

        $credentials['client_aud'] = $user[0]['username'];

        return $handler->handle($request->withParsedBody($credentials));
    }
}
