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
        string $pClientId = null,
        mixed $pParam1 = null,
        string $pParam2 = 'default',
        float $pParam3 = 5.3E-7,
        int $pParam4 = null,
        bool $pParam5 = null,
        array $pParam6 = null,
        string $hAzef = null,
        float $qAgrez = null,
        ?int $cAzgrzeg = 10,
        ?bool $hGegzer = true,
        GetClientRequestPayload $dto,
    ): GetClient200ApplicationJsonResponse|GetClient201ApplicationJsonResponse;
}
