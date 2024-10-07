<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests;

use Zol\Ogen\Tests\TestOpenApiServer\Api\Tag\FirstOperation200ApplicationJsonResponse;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Tag\FirstOperation200ApplicationJsonResponsePayload;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Tag\FirstOperationApplicationXWwwFormUrlencodedRequestBodyPayload;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Tag\TagHandler;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Schema;

class TestHandler implements TagHandler
{
    public function firstOperationFromSchemaPayloadToApplicationJsonContent(string $pPathParamString, float $pPathParamNumber, int $pPathParamInteger, bool $pPathParamBoolean, string $qQueryParamString, float $qQueryParamNumber, int $qQueryParamInteger, bool $qQueryParamBoolean, string $hHeaderParamString, float $hHeaderParamNumber, int $hHeaderParamInteger, bool $hHeaderParamBoolean, string $cCookieParamString, float $cCookieParamNumber, int $cCookieParamInteger, bool $cCookieParamBoolean, Schema $requestBodyPayload): FirstOperation200ApplicationJsonResponse
    {
        try {
            return new FirstOperation200ApplicationJsonResponse(
                new FirstOperation200ApplicationJsonResponsePayload(
                    json_encode([
                        'pathParamString' => $pPathParamString,
                        'pathParamNumber' => $pPathParamNumber,
                        'pathParamInteger' => $pPathParamInteger,
                        'pathParamBoolean' => $pPathParamBoolean,
                        'queryParamString' => $qQueryParamString,
                        'queryParamNumber' => $qQueryParamNumber,
                        'queryParamInteger' => $qQueryParamInteger,
                        'queryParamBoolean' => $qQueryParamBoolean,
                        'headerParamString' => $hHeaderParamString,
                        'headerParamNumber' => $hHeaderParamNumber,
                        'headerParamInteger' => $hHeaderParamInteger,
                        'headerParamBoolean' => $hHeaderParamBoolean,
                        'cookieParamString' => $cCookieParamString,
                        'cookieParamNumber' => $cCookieParamNumber,
                        'cookieParamInteger' => $cCookieParamInteger,
                        'cookieParamBoolean' => $cCookieParamBoolean,
                        'requestBodyPayload' => $requestBodyPayload,
                    ], \JSON_THROW_ON_ERROR),
                ),
                'string',
                .1,
                1,
                true,
            );
        } catch (\JsonException $e) {
            throw new \RuntimeException('Unexpected invalid JSON');
        }
    }

    public function firstOperationFromFirstOperationApplicationXWwwFormUrlencodedRequestBodyPayloadPayloadToApplicationJsonContent(string $pPathParamString, float $pPathParamNumber, int $pPathParamInteger, bool $pPathParamBoolean, string $qQueryParamString, float $qQueryParamNumber, int $qQueryParamInteger, bool $qQueryParamBoolean, string $hHeaderParamString, float $hHeaderParamNumber, int $hHeaderParamInteger, bool $hHeaderParamBoolean, string $cCookieParamString, float $cCookieParamNumber, int $cCookieParamInteger, bool $cCookieParamBoolean, FirstOperationApplicationXWwwFormUrlencodedRequestBodyPayload $requestBodyPayload): FirstOperation200ApplicationJsonResponse
    {
        try {
            return new FirstOperation200ApplicationJsonResponse(
                new FirstOperation200ApplicationJsonResponsePayload(
                    json_encode([
                        'pathParamString' => $pPathParamString,
                        'pathParamNumber' => $pPathParamNumber,
                        'pathParamInteger' => $pPathParamInteger,
                        'pathParamBoolean' => $pPathParamBoolean,
                        'queryParamString' => $qQueryParamString,
                        'queryParamNumber' => $qQueryParamNumber,
                        'queryParamInteger' => $qQueryParamInteger,
                        'queryParamBoolean' => $qQueryParamBoolean,
                        'headerParamString' => $hHeaderParamString,
                        'headerParamNumber' => $hHeaderParamNumber,
                        'headerParamInteger' => $hHeaderParamInteger,
                        'headerParamBoolean' => $hHeaderParamBoolean,
                        'cookieParamString' => $cCookieParamString,
                        'cookieParamNumber' => $cCookieParamNumber,
                        'cookieParamInteger' => $cCookieParamInteger,
                        'cookieParamBoolean' => $cCookieParamBoolean,
                        'requestBodyPayload' => $requestBodyPayload,
                    ], \JSON_THROW_ON_ERROR),
                ),
                'string',
                .1,
                1,
                true,
            );
        } catch (\JsonException $e) {
            throw new \RuntimeException('Unexpected invalid JSON');
        }
    }
}
