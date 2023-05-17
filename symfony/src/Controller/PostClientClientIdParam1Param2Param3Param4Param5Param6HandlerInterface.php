<?php

namespace App\Controller;

interface PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $pParam1,
        string $pParam2,
    ):
        PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJson |
        PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJson;
}
