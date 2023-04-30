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
        string $clientId = null,
        mixed $param1 = null,
        string $param2 = 'default',
        float $param3 = 5.3E-7,
        int $param4 = null,
        bool $param5 = null,
        array $param6 = null,
        string $azef = null,
        float $agrez = null,
        ?int $azgrzeg = 10,
        ?bool $gegzer = true,
        GetClientRequestPayload $dto,
    ): GetClient200ApplicationJsonResponse|GetClient201ApplicationJsonResponse;
}
