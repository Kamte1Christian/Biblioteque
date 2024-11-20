<?php
// src/OpenApi/AbonnementDecorator.php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;

class AbonnementDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $paths = $openApi->getPaths();

        $operation = new Operation(
            operationId: 'postCreateAbonnement',
            tags: ['Abonnements'],
            responses: [
                '201' => [
                    'description' => 'Abonnement created successfully',
                ],
            ],
            summary: 'Create an Abonnement',
            description: 'Creates a new abonnement with automatic date calculation',
            requestBody: new RequestBody(
                description: 'Input data for creating an Abonnement',
                content: new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'userId' => ['type' => 'integer'],
                                'typeAbonnementId' => ['type' => 'integer'],
                            ],
                        ],
                    ],
                ])
            )
        );

        $pathItem = new PathItem(
            post: $operation
        );

        $paths->addPath('/abonnements/create', $pathItem);

        return $openApi;
    }
}
