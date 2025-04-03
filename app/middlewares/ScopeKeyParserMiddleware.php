<?php

namespace App\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ScopeKeyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contents = $request->getParsedBody();

        // SCOPE KEY VALIDATION
        if (!array_key_exists('scope', $contents) || (empty($contents['scope']))) {
            throw new HttpBadRequestException($request);
        }

        // SCOPE STRUCTURE VALIDATION
        $scope = [];
        $ok = true;
        foreach ($contents['scope'] as $s => $value) {
            $ok &= ($s !== '');
            $ok &= in_array(strtolower($value), ['get', 'post', 'put', 'patch', 'delete']);
            if ($ok) {
                array_push($scope, $s . '/' . $value);
            }
        }
        if (!$ok) {
            throw new HttpBadRequestException($request);
        }

        $contents['scope'] = $scope;

        return $handler->handle($request->withParsedBody($contents));
    }
}
