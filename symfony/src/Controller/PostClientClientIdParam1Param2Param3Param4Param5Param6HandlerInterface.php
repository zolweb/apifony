<?php

namespace App\Controller;

interface PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface
{
    /**
     * OperationId: post-client-clientId-param1-param2-param3-param4-param5-param6
     */
    public function handle(
        string $pClientId = null,
        mixed $pParam1 = null,
        string $pParam2 = 'default',
        float $pParam3 = 5.3E-7,
        int $pParam4 = null,
        bool $pParam5 = null,
        array $pParam6 = null,
    ): PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJsonResponse|PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse;
}