<?php

use App\controllers\TokenController;
use App\controllers\CredentialController;
use App\middlewares\JsonBodyParserMiddleware;
use App\middlewares\ScopeKeyParserMiddleware;
use App\middlewares\ClientKeyParserMiddleware;
use App\middlewares\UserAuthorizationMiddleware;
use App\middlewares\CredentialDBValidationMiddleware;
use App\middlewares\CredentialBasicValidationMiddleware;

$app->post('/api/credentials', [CredentialController::class, 'create'])
    ->add(new ClientKeyParserMiddleware())
    ->add(new JsonBodyParserMiddleware())
    ->add(new UserAuthorizationMiddleware())
    ->add(new CredentialBasicValidationMiddleware());
$app->post('/api/token', [TokenController::class, 'create'])
    ->add(new ScopeKeyParserMiddleware())
    ->add(new JsonBodyParserMiddleware())
    ->add(new CredentialDBValidationMiddleware())
    ->add(new CredentialBasicValidationMiddleware());
