<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * @internal
 */
final class ApifonyTest extends WebTestCase
{
    public function testA(): void
    {
        $httpClient = self::createClient();
        $httpClient->catchExceptions(false);

        $httpClient->getCookieJar()->set(new Cookie('cookieParamString', 'string'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamNumber', '0.1'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamInteger', '1'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamBoolean', 'true'));

        $httpClient->jsonRequest(
            method: 'POST',
            uri: '/path/string/0.1/1/true?queryParamString=string&queryParamNumber=0.1&queryParamInteger=1&queryParamBoolean=true',
            parameters: [
                'stringProperty' => 'string',
                'numberProperty' => 0.1,
                'integerProperty' => 1,
                'booleanProperty' => true,
                'emailProperty' => 'erwin.schrödinger@zol.fr',
                'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                'dateProperty' => '1969-07-21',
                'timeProperty' => '03:56:20+01:00',
                'customProperty' => 'custom',
                'overriddenProperty' => 'abc',
                'objectProperty' => [
                    'stringProperty' => 'string',
                ],
                'arrayProperty' => ['string'],
                'objectArrayProperty' => [
                    ['stringProperty' => 'string'],
                ],
                'recursiveObjectArray' => [
                    [
                        'stringProperty' => 'string',
                        'numberProperty' => 0.1,
                        'integerProperty' => 1,
                        'booleanProperty' => true,
                        'emailProperty' => 'erwin.schrödinger@zol.fr',
                        'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                        'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                        'dateProperty' => '1969-07-21',
                        'timeProperty' => '03:56:20+01:00',
                        'customProperty' => 'custom',
                        'overriddenProperty' => 'abc',
                        'objectProperty' => [
                            'stringProperty' => 'string',
                        ],
                        'arrayProperty' => ['string'],
                        'objectArrayProperty' => [
                            ['stringProperty' => 'string'],
                        ],
                        'recursiveObjectArray' => [],
                    ],
                ],
            ],
            server: [
                'HTTP_HEADERPARAMSTRING' => 'string',
                'HTTP_HEADERPARAMNUMBER' => '0.1',
                'HTTP_HEADERPARAMINTEGER' => '1',
                'HTTP_HEADERPARAMBOOLEAN' => 'true',
            ],
        );

        $rawContent = $httpClient->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertIsString($rawContent);
        $content = json_decode($rawContent, true);
        self::assertIsArray($content);
        self::assertArrayHasKey('dump', $content);
        self::assertIsString($content['dump']);
        $dump = json_decode($content['dump'], true);

        self::assertEqualsCanonicalizing(
            [
                'pathParamString' => 'string',
                'pathParamNumber' => 0.1,
                'pathParamInteger' => 1,
                'pathParamBoolean' => true,
                'queryParamString' => 'string',
                'queryParamNumber' => 0.1,
                'queryParamInteger' => 1,
                'queryParamBoolean' => true,
                'headerParamString' => 'string',
                'headerParamNumber' => 0.1,
                'headerParamInteger' => 1,
                'headerParamBoolean' => true,
                'cookieParamString' => 'string',
                'cookieParamNumber' => 0.1,
                'cookieParamInteger' => 1,
                'cookieParamBoolean' => true,
                'requestBodyPayload' => [
                    'stringProperty' => 'string',
                    'numberProperty' => 0.1,
                    'integerProperty' => 1,
                    'booleanProperty' => true,
                    'emailProperty' => 'erwin.schrödinger@zol.fr',
                    'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                    'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                    'dateProperty' => '1969-07-21',
                    'timeProperty' => '03:56:20+01:00',
                    'customProperty' => 'custom',
                    'nullDefaultProperty' => null,
                    'objectProperty' => [
                        'stringProperty' => 'string',
                    ],
                    'arrayProperty' => ['string'],
                    'objectArrayProperty' => [
                        [
                            'stringProperty' => 'string',
                        ],
                    ],
                    'recursiveObjectArray' => [
                        [
                            'stringProperty' => 'string',
                            'numberProperty' => 0.1,
                            'integerProperty' => 1,
                            'booleanProperty' => true,
                            'emailProperty' => 'erwin.schrödinger@zol.fr',
                            'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                            'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                            'dateProperty' => '1969-07-21',
                            'timeProperty' => '03:56:20+01:00',
                            'customProperty' => 'custom',
                            'nullDefaultProperty' => null,
                            'objectProperty' => [
                                'stringProperty' => 'string',
                            ],
                            'arrayProperty' => ['string'],
                            'objectArrayProperty' => [
                                [
                                    'stringProperty' => 'string',
                                ],
                            ],
                            'recursiveObjectArray' => [],
                            'defaultProperty' => 'abc',
                            'overriddenProperty' => 'abc',
                        ],
                    ],
                    'defaultProperty' => 'abc',
                    'overriddenProperty' => 'abc',
                ],
            ],
            $dump,
        );
    }
}
