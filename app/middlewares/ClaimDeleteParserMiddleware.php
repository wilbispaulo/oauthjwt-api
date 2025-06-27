<?php

namespace App\middlewares;

use App\models\Endpoint;
use Slim\Routing\RouteContext;
use Wilbispaulo\DBmodel\lib\DBFilters;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ClaimDeleteParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();

        $endpoint = new Endpoint();
        $filter = new DBFilters();

        if (array_key_exists('claim', $args)) {
            $claimEndpoint = substr($args['claim'], 0, strrpos($args['claim'], '/'));
            $claimMethod = substr($args['claim'], strrpos($args['claim'], '/') + 1);
            $filter->where('clientid', '=', $args['clientid'], 'and');
            $filter->where('endpoint', '=', $claimEndpoint, 'and');
            $filter->where('method', '=', $claimMethod);
        } else {
            $filter->where('clientid', '=', $args['clientid']);
        }

        $endpoint->setFilters($filter);

        if (count($endpoint->findBy()) === 0) {
            throw new HttpNotFoundException($request);
        }

        return $handler->handle($request);
    }
}
