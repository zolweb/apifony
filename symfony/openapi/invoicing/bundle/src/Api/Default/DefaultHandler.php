<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

interface DefaultHandler
{
    public function GetClientFromEmptyPayloadToApplicationJsonContent(
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

    public function GetClientFromIntegerPayloadToApplicationJsonContent(
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

    public function GetClientFromGetClientApplicationXmlRequestBodyPayloadPayloadToApplicationJsonContent(
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
        GetClientApplicationXmlRequestBodyPayload $requestBodyPayload,
    ):
        GetClient200ApplicationJsonResponse |
        GetClient201ApplicationJsonResponse;

    public function PostClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToApplicationJsonContent(
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        string $pParam1,
        string $pParam2,
    ):
        PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJsonResponse |
        PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse;

    public function PostTestFromEmptyPayloadToApplicationJsonContent(
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

    public function PostTestFromEmptyPayloadToApplicationXmlContent(
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
        PostTest200ApplicationXmlResponse;

    public function PostTestFromPostTestApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
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
        PostTestApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PostTest200ApplicationJsonResponse |
        PostTest201ApplicationJsonResponse;

    public function PostTestFromPostTestApplicationJsonRequestBodyPayloadPayloadToApplicationXmlContent(
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
        PostTestApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PostTest200ApplicationXmlResponse;

    public function PostTestFromPostTestApplicationXmlRequestBodyPayloadPayloadToApplicationJsonContent(
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
        PostTestApplicationXmlRequestBodyPayload $requestBodyPayload,
    ):
        PostTest200ApplicationJsonResponse |
        PostTest201ApplicationJsonResponse;

    public function PostTestFromPostTestApplicationXmlRequestBodyPayloadPayloadToApplicationXmlContent(
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
        PostTestApplicationXmlRequestBodyPayload $requestBodyPayload,
    ):
        PostTest200ApplicationXmlResponse;

    public function PostTestFromPostTestMultipartFormDataRequestBodyPayloadPayloadToApplicationJsonContent(
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
        PostTestMultipartFormDataRequestBodyPayload $requestBodyPayload,
    ):
        PostTest200ApplicationJsonResponse |
        PostTest201ApplicationJsonResponse;

    public function PostTestFromPostTestMultipartFormDataRequestBodyPayloadPayloadToApplicationXmlContent(
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
        PostTestMultipartFormDataRequestBodyPayload $requestBodyPayload,
    ):
        PostTest200ApplicationXmlResponse;
}
