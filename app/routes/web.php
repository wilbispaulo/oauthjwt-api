<?php

use App\controllers\ClaimController;
use App\controllers\TokenController;
use App\controllers\CredentialController;
use App\middlewares\ScopeParserMiddleware;
use App\middlewares\JsonBodyParserMiddleware;
use App\middlewares\ScopeKeyParserMiddleware;
use App\middlewares\BodyBasicParserMiddleware;
use App\middlewares\ClientKeyParserMiddleware;
use App\middlewares\ClaimDeleteParserMiddleware;
use App\middlewares\UserAuthorizationMiddleware;
use App\middlewares\ClientIdDBValidationMiddleware;
use App\middlewares\CredentialDBValidationMiddleware;
use App\middlewares\CredentialBasicValidationMiddleware;

$app->post('/api/credentials', [CredentialController::class, 'create'])
    ->add(new ClientKeyParserMiddleware())
    ->add(new JsonBodyParserMiddleware())
    ->add(new UserAuthorizationMiddleware())
    ->add(new CredentialBasicValidationMiddleware());
$app->post('/api/claims', [ClaimController::class, 'create'])
    ->add(new ClientIdDBValidationMiddleware())
    ->add(new BodyBasicParserMiddleware())
    ->add(new JsonBodyParserMiddleware())
    ->add(new UserAuthorizationMiddleware())
    ->add(new CredentialBasicValidationMiddleware());
$app->delete('/api/claims/{clientid}[/{claim:.*}]', [ClaimController::class, 'delete'])
    ->add(new ClaimDeleteParserMiddleware())
    ->add(new UserAuthorizationMiddleware())
    ->add(new CredentialBasicValidationMiddleware());
$app->get('/api/token', [TokenController::class, 'create'])
    ->add(new ScopeParserMiddleware())
    ->add(new CredentialDBValidationMiddleware())
    ->add(new CredentialBasicValidationMiddleware());
