<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

use Zol\Apifony\Tests\TestOpenApiServer\Api\Tag\FirstOperation200Response;
use Zol\Apifony\Tests\TestOpenApiServer\Api\Tag\FirstOperation200ResponsePayload;
use Zol\Apifony\Tests\TestOpenApiServer\Api\Tag\TagHandler;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;

class TestHandler implements TagHandler
{
    public function firstOperation(string $pPathParamString, float $pPathParamNumber, int $pPathParamInteger, bool $pPathParamBoolean, string $qQueryParamString, float $qQueryParamNumber, int $qQueryParamInteger, bool $qQueryParamBoolean, string $hHeaderParamString, float $hHeaderParamNumber, int $hHeaderParamInteger, bool $hHeaderParamBoolean, string $cCookieParamString, float $cCookieParamNumber, int $cCookieParamInteger, bool $cCookieParamBoolean, Schema $requestBodyPayload): FirstOperation200Response
    {
        try {
            return new FirstOperation200Response(
                new FirstOperation200ResponsePayload(
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
                    'string',
                    new Abc('abc1'),
                    new Abc('abc2'),
                    [new Abc('abc11'), new Abc('abc12')],
                    [new Abc('abc21'), new Abc('abc22')],
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
