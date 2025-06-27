<?php

namespace App\controllers;

use App\models\Endpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Wilbispaulo\DBmodel\lib\DBFilters;

class ClaimController
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $contents = $request->getParsedBody();

        $endpointsAssoc = [];
        $endpoints = new Endpoint();

        $record = [];
        $n = 0;

        $filter = new DBFilters();

        foreach ($contents['claims'] as $claim) {
            $endpointsAssoc['clientid'] = $contents['clientid'];
            $endpointsAssoc['endpoint'] = substr($claim, 0, strrpos($claim, '/'));
            $endpointsAssoc['method'] = substr($claim, strrpos($claim, '/') + 1);
            $filter->clear();
            $filter->where('clientid', '=', $contents['clientid'], 'and');
            $filter->where('endpoint', '=', $endpointsAssoc['endpoint'], 'and');
            $filter->where('method', '=', $endpointsAssoc['method']);
            $endpoints->setFilters($filter);
            if (count($endpoints->findBy()) === 0) {
                if (!$endpoints->create($endpointsAssoc)) {
                    throw new HttpInternalServerErrorException($request);
                }
                $record[(string)$n] = $claim;
                $n++;
            }
        }

        $response->getBody()->write(json_encode(['clientid' => $contents['clientid'], 'claims_rec' => $record], JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $endpoint = new Endpoint();
        $filter = new DBFilters();

        $msg = [];
        $msg['clientid'] = $args['clientid'];

        if (array_key_exists('claim', $args)) {
            $claimEndpoint = substr($args['claim'], 0, strrpos($args['claim'], '/'));
            $claimMethod = substr($args['claim'], strrpos($args['claim'], '/') + 1);
            $filter->where('clientid', '=', $args['clientid'], 'and');
            $filter->where('endpoint', '=', $claimEndpoint, 'and');
            $filter->where('method', '=', $claimMethod);
            $msg['claim'] = $args['claim'];
        } else {
            $filter->where('clientid', '=', $args['clientid']);
        }

        $endpoint->setFilters($filter);

        if (!$endpoint->delete()) {
            throw new HttpInternalServerErrorException($request);
        };

        $response->getBody()->write(json_encode($msg, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
