<?php

namespace App\Controller;

interface DefaultHandlerInterface
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

    public function handleEmptyPayloadToApplicationJsonContent(
        string $cC1,
        int $cC2,
        float $cC3,
        bool $cC4,
        string $hH1,
        int $hH2,
        float $hH3,
        bool $hH4,
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
        string $qQ1,
        int $qQ2,
        float $qQ3,
        bool $qQ4,
    ):
        PostTest200ApplicationJson |
        PostTest201ApplicationJson;

    public function handleTestPayloadToApplicationJsonContent(
        string $cC1,
        int $cC2,
        float $cC3,
        bool $cC4,
        string $hH1,
        int $hH2,
        float $hH3,
        bool $hH4,
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
        string $qQ1,
        int $qQ2,
        float $qQ3,
        bool $qQ4,
        Test $requestBodyPayload,
    ):
        PostTest200ApplicationJson |
        PostTest201ApplicationJson;
}
