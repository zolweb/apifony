<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\Tag;

use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;
interface TagHandler
{
    public function firstOperation(string $pPathParamString, float $pPathParamNumber, int $pPathParamInteger, bool $pPathParamBoolean, string $qQueryParamString, float $qQueryParamNumber, int $qQueryParamInteger, bool $qQueryParamBoolean, string $hHeaderParamString, float $hHeaderParamNumber, int $hHeaderParamInteger, bool $hHeaderParamBoolean, string $cCookieParamString, float $cCookieParamNumber, int $cCookieParamInteger, bool $cCookieParamBoolean, Schema $requestBodyPayload): FirstOperation200Response;
}