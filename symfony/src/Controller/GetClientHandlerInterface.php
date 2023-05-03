<?php

namespace App\Controller;

interface GetClientHandlerInterface
{
    /**
     * OperationId: get-client
     *
     * Your GET endpoint
     */
    public function handle(
        string $pClientId,
        mixed $pParam1,
        string $pParam2 = 'default',
        float $pParam3 = 5.3E-7,
        int $pParam4,
        bool $pParam5,
        array $pParam6,
        string $hAzef,
        float $qAgrez,
        ?int $cAzgrzeg = 10,
        ?bool $hGegzer = true,
    ): GetClient200ApplicationJsonResponse|GetClient201ApplicationJsonResponse;
}