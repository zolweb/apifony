<?php

namespace App\Controller;

interface GetClientHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        float $qAgrez,
        string $hAzef,
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        int $cAzgrzeg,
        bool $hGegzer,
        string $pParam1,
        string $pParam2,
    ):
        TestRespApplicationJson |
        GetClient201ApplicationJson;

    public function handleIntegerPayloadToApplicationJsonContent(
        float $qAgrez,
        string $hAzef,
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        int $cAzgrzeg,
        bool $hGegzer,
        string $pParam1,
        string $pParam2,
        int $requestBodyPayload,
    ):
        TestRespApplicationJson |
        GetClient201ApplicationJson;
}
