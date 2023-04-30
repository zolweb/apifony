<?php

namespace App\Controller;

interface PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface
{
    /**
     * OperationId: post-client-clientId-param1-param2-param3-param4-param5-param6
     */
    public function handle(
        string $clientId = null,
        mixed $param1 = null,
        string $param2 = 'default',
        float $param3 = 5.3E-7,
        int $param4 = null,
        bool $param5 = null,
        array $param6 = null,
    ): PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJsonResponse|PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse;
}
