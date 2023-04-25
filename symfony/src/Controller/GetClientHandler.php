<?php

namespace App\Controller;

interface GetClientHandler
{
    public function handle(
        string $clientId,
        mixed $param1,
        string $param2,
        float $param3,
        int $param4,
        bool $param5,
        array $param6,
        GetClientRequestPayload $dto,
    ): void
}}
