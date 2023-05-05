<?php

namespace App\Controller;

interface PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface
{
    /**
     * OperationId: post-client-clientId-param1-param2-param3-param4-param5-param6
     */
    public function handle(
        string $pClientId,
        int $pParam4,
        bool $pParam5,
        array $pParam6,
        mixed $pParam1,
        string $pParam2 = 'default',
        float $pParam3 = 5.3E-7,
    ): PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJsonResponse|PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse;
}