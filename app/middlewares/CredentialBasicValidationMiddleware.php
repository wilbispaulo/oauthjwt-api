<?php

namespace App\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;

class CredentialBasicValidationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // CREDENTIALS VALIDATION BASIC
        $authorizationToken = $request->getHeaderLine('Authorization');
        if ($ok = ($authorizationToken !== '')) {
            $authorizationToken = explode(' ', $authorizationToken);
            if ($ok = (strtolower($authorizationToken[0]) === 'basic')) {
                $credentials = explode(':', base64_decode($authorizationToken[1]));
                [$username, $password] = $credentials;
                $ok = ($username !== '' && $password !== '');
            }
        }
        if (!$ok) {
            throw new HttpUnauthorizedException($request);
        }

        $credentials = ['username' => $username, 'password' => $password];

        return $handler->handle($request->withParsedBody($credentials));
    }
}
