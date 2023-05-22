<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

interface DefaultHandler
{
    public function GetClientFromEmptyPayloadToApplicationJsonContent(
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
        GetClient200ApplicationJson |
        GetClient201ApplicationJson;

    public function GetClientFromIntegerPayloadToApplicationJsonContent(
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
        GetClient200ApplicationJson |
        GetClient201ApplicationJson;

    public function PostClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToApplicationJsonContent(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $pParam1,
        string $pParam2,
    ):
        PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJson |
        PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJson;

    public function PostTestFromEmptyPayloadToApplicationJsonContent(
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

    public function PostTestFromTestPayloadToApplicationJsonContent(
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
