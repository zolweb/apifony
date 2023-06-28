<?php

namespace AppZolInvoicingPresentationApiBundle\Api\Default;

use AppZolInvoicingPresentationApiBundle\Model\Test;

interface DefaultHandler
{
    public function getClientFromEmptyPayloadToApplicationJsonContent(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $hAzef,
        float $qAgrez,
        string $pParam1,
        string $pParam2,
        int $cAzgrzeg,
        bool $hGegzer,
    ):
        GetClient200ApplicationJsonResponse |
        GetClient201ApplicationJsonResponse;

    public function getClientFromIntegerPayloadToApplicationJsonContent(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $hAzef,
        float $qAgrez,
        string $pParam1,
        string $pParam2,
        int $cAzgrzeg,
        bool $hGegzer,
        int $requestBodyPayload,
    ):
        GetClient200ApplicationJsonResponse |
        GetClient201ApplicationJsonResponse;

    public function postClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToApplicationJsonContent(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $pParam1,
        string $pParam2,
    ):
        PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJsonResponse |
        PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse;

    public function postTestFromEmptyPayloadToApplicationJsonContent(
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
        string $hH1,
        int $hH2,
        float $hH3,
        bool $hH4,
        string $qQ1,
        int $qQ2,
        float $qQ3,
        bool $qQ4,
        string $cC1,
        int $cC2,
        float $cC3,
        bool $cC4,
    ):
        PostTest200ApplicationJsonResponse |
        PostTest201ApplicationJsonResponse;

    public function postTestFromTestPayloadToApplicationJsonContent(
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
        string $hH1,
        int $hH2,
        float $hH3,
        bool $hH4,
        string $qQ1,
        int $qQ2,
        float $qQ3,
        bool $qQ4,
        string $cC1,
        int $cC2,
        float $cC3,
        bool $cC4,
        Test $requestBodyPayload,
    ):
        PostTest200ApplicationJsonResponse |
        PostTest201ApplicationJsonResponse;
}
