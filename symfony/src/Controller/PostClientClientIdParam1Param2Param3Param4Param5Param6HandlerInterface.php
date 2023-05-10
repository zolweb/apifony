<?php

namespace App\Controller;

interface PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface
{
    /**
     * OperationId: post-client-clientId-param1-param2-param3-param4-param5-param6
     *
     * desc
     */
    public function handle(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        mixed $pParam1,
        string $pParam2 = 'item',
    ): PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJsonResponse|PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse;
}