<?php

namespace App\Controller;

interface PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface
{
    public function handleEmptyApplicationJson(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $pParam1,
        string $pParam2,
    ):
        PostClientClientIdParam1Param2Param3Param4Param5Param6200String |
        PostClientClientIdParam1Param2Param3Param4Param5Param6201PostClientClientIdParam1Param2Param3Param4Param5Param6ApplicationJsonMediaTypeSchema;
}
