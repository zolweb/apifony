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
        float $qAgrez,
        string $hAzef,
        string $pClientId,
        int $pParam4,
        bool $pParam5,
        array $pParam6,
        ?int $cAzgrzeg = 10,
        ?bool $hGegzer = true,
        mixed $pParam1,
        string $pParam2 = 'default',
        float $pParam3 = 5.3E-7,
    ): GetClient200ApplicationJsonResponse|GetClient201ApplicationJsonResponse;
}