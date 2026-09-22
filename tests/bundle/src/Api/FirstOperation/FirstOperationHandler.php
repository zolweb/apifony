<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
interface FirstOperationHandler
{
    /**
     * @param list<string> $qQueryParamStringArray
     * @param list<list<int<min,max>>> $qQueryParamIntegerMatrix
     * @param list<Abc> $qQueryParamAbcList
     * @param list<string> $qQueryParamOptionalArray
     * @param ?list<bool> $qQueryParamNullableArray
     */
    public function firstOperation(string $pPathParamString, float $pPathParamNumber, int $pPathParamInteger, bool $pPathParamBoolean, string $qQueryParamString, float $qQueryParamNumber, int $qQueryParamInteger, bool $qQueryParamBoolean, string $hHeaderParamString, float $hHeaderParamNumber, int $hHeaderParamInteger, bool $hHeaderParamBoolean, string $cCookieParamString, float $cCookieParamNumber, int $cCookieParamInteger, bool $cCookieParamBoolean, array $qQueryParamStringArray, array $qQueryParamIntegerMatrix, array $qQueryParamAbcList, FirstOperationQueryParamObject $qQueryParamObject, Abc $qQueryParamAbcRef, array $qQueryParamOptionalArray, ?array $qQueryParamNullableArray, Schema $requestBodyPayload): FirstOperation200Response;
}