<?php

namespace App\middlewares;

use App\library\Connection;
use App\models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;

class UserAuthorizationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $credentials = $request->getParsedBody();

        $user = new User();
        $userData = $user->findBy('email', $credentials['username']);
        if ($userData === false) {
            throw new HttpInternalServerErrorException($request);
        }
        if (count($userData) === 0) {
            throw new HttpUnauthorizedException($request);
        }
        if (password_verify($credentials['password'], $userData[0]['password'])) {
            $credentials['authorization'] = 'pass';
        } else {
            throw new HttpUnauthorizedException($request);
        }
        unset($credentials['password']);

        return $handler->handle($request->withParsedBody($credentials));
    }
}
