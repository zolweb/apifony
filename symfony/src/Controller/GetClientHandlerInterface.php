<?php

namespace App\Controller;

interface GetClientHandlerInterface
{
    /**
     * OperationId: get-client
     *
     * Your GET endpoint
     *
     * desc
     */
    public function handle(
        float $qAgrez,
        string $hAzef,
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        ?int $cAzgrzeg = 10,
        ?bool $hGegzer = true,
        mixed $pParam1,
        string $pParam2 = 'item',
    ): GetClient200ApplicationJsonResponse|GetClient201ApplicationJsonResponse;
}