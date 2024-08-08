<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Tag;

use Zol\Ogen\Tests\TestOpenApiServer\Model\Schema;
interface TagHandler
{
    public function firstOperationFromSchemaPayloadToApplicationJsonContent(string $pPathParamString, float $pPathParamNumber, int $pPathParamInteger, bool $pPathParamBoolean, string $qQueryParamString, float $qQueryParamNumber, int $qQueryParamInteger, bool $qQueryParamBoolean, string $hHeaderParamString, float $hHeaderParamNumber, int $hHeaderParamInteger, bool $hHeaderParamBoolean, string $cCookieParamString, float $cCookieParamNumber, int $cCookieParamInteger, bool $cCookieParamBoolean, Schema $requestBodyPayload): FirstOperation200ApplicationJsonResponse;
}