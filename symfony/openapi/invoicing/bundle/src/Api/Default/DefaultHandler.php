<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

interface DefaultHandler
{
    public function GetClientFromEmptyPayloadToContent(
        string $pclientId,
        float $pparam3,
        int $pparam4,
        bool $pparam5,
        string $hazef,
        float $qagrez,
        string $pparam1,
        string $pparam2,
        int $cazgrzeg,
        bool $hgegzer,
    ):
        GetClient200EmptyResponse |
        GetClient201EmptyResponse;

    public function PostClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToContent(
        string $pclientId,
        float $pparam3,
        int $pparam4,
        bool $pparam5,
        string $pparam1,
        string $pparam2,
    ):
        PostClientClientIdParam1Param2Param3Param4Param5Param6200EmptyResponse |
        PostClientClientIdParam1Param2Param3Param4Param5Param6201EmptyResponse;

    public function PostTestFromEmptyPayloadToContent(
        string $pp1,
        int $pp2,
        float $pp3,
        bool $pp4,
        string $hh1,
        int $hh2,
        float $hh3,
        bool $hh4,
        string $qq1,
        int $qq2,
        float $qq3,
        bool $qq4,
        string $cc1,
        int $cc2,
        float $cc3,
        bool $cc4,
    ):
        PostTest200EmptyResponse |
        PostTest201EmptyResponse;
}
