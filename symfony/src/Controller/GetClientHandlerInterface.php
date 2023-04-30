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
        string $clientId,
        mixed $param1,
        string $param2 = 'default',
        float $param3 = 5.3E-7,
        int $param4,
        bool $param5,
        array $param6,
        GetClientRequestPayload $dto,
    ): GetClient200ApplicationJsonResponse|GetClient201ApplicationJsonResponse;
}
