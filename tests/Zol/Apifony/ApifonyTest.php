<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * @internal
 *
 * @coversNothing
 */
final class ApifonyTest extends WebTestCase
{
    private const VALID_QUERY = '?queryParamString=string&queryParamNumber=0.1&queryParamInteger=1&queryParamBoolean=true'
        .'&queryParamStringArray[]=aa&queryParamStringArray[]=bb'
        .'&queryParamIntegerMatrix[0][]=1&queryParamIntegerMatrix[0][]=2&queryParamIntegerMatrix[1][]=3'
        .'&queryParamAbcList[0][def]=x&queryParamAbcList[1][def]=y'
        .'&queryParamObject[stringProperty]=s&queryParamObject[nestedArrayProperty][]=1'
        .'&queryParamObject[nestedObjectProperty][emailProperty]=erwin@zol.fr'
        .'&queryParamAbcRef[def]=z'
        .'&queryParamNodeTree[name]=root'
        .'&queryParamNodeTree[children][0][name]=a'
        .'&queryParamNodeTree[children][0][children][0][name]=a1'
        .'&queryParamNodeTree[children][1][name]=b';

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
            uri: '/path/string/0.1/1/true'.self::VALID_QUERY,
            parameters: self::getRequestBody(),
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
                'queryParamStringArray' => ['aa', 'bb'],
                'queryParamIntegerMatrix' => [[1, 2], [3]],
                'queryParamAbcList' => [['def' => 'x'], ['def' => 'y']],
                'queryParamObject' => [
                    'stringProperty' => 's',
                    'nestedArrayProperty' => [1],
                    'nestedObjectProperty' => ['emailProperty' => 'erwin@zol.fr'],
                    'optionalProperty' => 'abc',
                ],
                'queryParamAbcRef' => ['def' => 'z'],
                'queryParamNodeTree' => [
                    'name' => 'root',
                    'children' => [
                        ['name' => 'a', 'children' => [['name' => 'a1', 'children' => []]]],
                        ['name' => 'b', 'children' => []],
                    ],
                ],
                'queryParamOptionalArray' => [],
                'queryParamNullableArray' => null,
                'requestBodyPayload' => [
                    'stringProperty' => 'string',
                    'numberProperty' => 0.1,
                    'integerProperty' => 1,
                    'booleanProperty' => true,
                    'enumStringProperty' => 'def',
                    'enumNullableStringProperty' => null,
                    'integerRangeProperty' => 0,
                    'emailProperty' => 'erwin.schrödinger@zol.fr',
                    'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                    'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                    'dateTimeProperty2' => '1969-07-21T03:56:20.001+01:00',
                    'dateTimeProperty3' => '1969-07-21T03:56:20Z',
                    'dateTimeProperty4' => '1969-07-21T03:56:20.001Z',
                    'dateProperty' => '1969-07-21',
                    'timeProperty' => '03:56:20+01:00',
                    'timeProperty2' => '03:56:20.001+01:00',
                    'timeProperty3' => '03:56:20Z',
                    'timeProperty4' => '03:56:20.001Z',
                    'customProperty' => 'custom',
                    'nullDefaultProperty' => null,
                    'emptyArrayDefaultProperty' => [],
                    'objectProperty' => [
                        'stringProperty' => 'string',
                    ],
                    'arrayProperty' => ['string'],
                    'integerMatrixProperty' => [[1, 2], [3]],
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
                            'enumStringProperty' => 'def',
                            'enumNullableStringProperty' => null,
                            'integerRangeProperty' => 0,
                            'emailProperty' => 'erwin.schrödinger@zol.fr',
                            'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                            'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                            'dateTimeProperty2' => '1969-07-21T03:56:20.001+01:00',
                            'dateTimeProperty3' => '1969-07-21T03:56:20Z',
                            'dateTimeProperty4' => '1969-07-21T03:56:20.001Z',
                            'dateProperty' => '1969-07-21',
                            'timeProperty' => '03:56:20+01:00',
                            'timeProperty2' => '03:56:20.001+01:00',
                            'timeProperty3' => '03:56:20Z',
                            'timeProperty4' => '03:56:20.001Z',
                            'customProperty' => 'custom',
                            'nullDefaultProperty' => null,
                            'emptyArrayDefaultProperty' => [],
                            'objectProperty' => [
                                'stringProperty' => 'string',
                            ],
                            'arrayProperty' => ['string'],
                            'integerMatrixProperty' => [[1, 2], [3]],
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

    public function testB(): void
    {
        $httpClient = self::createClient();
        $httpClient->catchExceptions(false);

        $httpClient->getCookieJar()->set(new Cookie('cookieParamString', 'string'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamNumber', '0.1'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamInteger', '1'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamBoolean', 'true'));

        $httpClient->jsonRequest(
            method: 'POST',
            uri: '/path/string/0.1/1/true'.self::VALID_QUERY,
            parameters: self::getRequestBody(['dateTimeProperty' => '1969-error-21T03:56:20+01:00']),
            server: [
                'HTTP_HEADERPARAMSTRING' => 'string',
                'HTTP_HEADERPARAMNUMBER' => '0.1',
                'HTTP_HEADERPARAMINTEGER' => '1',
                'HTTP_HEADERPARAMBOOLEAN' => 'true',
            ],
        );

        $rawContent = $httpClient->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400);
        self::assertIsString($rawContent);
        $content = json_decode($rawContent, true);
        self::assertIsArray($content);

        self::assertEqualsCanonicalizing(
            [
                'code' => 'validation_failed',
                'message' => 'Validation has failed.',
                'errors' => [
                    'requestBody' => [
                        'dateTimeProperty' => [
                            'This is not a valid date time format according to RFC 3339.',
                        ],
                    ],
                ],
            ],
            $content,
        );
    }

    /**
     * A wrong leaf type nested two levels deep in a request body must be rejected, not silently
     * kept as a string inside a list<list<int>>.
     */
    public function testE(): void
    {
        $httpClient = self::createClientForQuery(
            self::VALID_QUERY,
            self::getRequestBody(['integerMatrixProperty' => [['abc']]]),
        );

        $rawContent = $httpClient->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400);
        self::assertIsString($rawContent);
        $content = json_decode($rawContent, true);
        self::assertIsArray($content);
        self::assertSame('validation_failed', $content['code'] ?? null);
        self::assertIsArray($content['errors'] ?? null);
        self::assertIsArray($content['errors']['requestBody'] ?? null);
        self::assertIsString($content['errors']['requestBody'][0] ?? null);
        self::assertStringContainsString('must be one of "int"', $content['errors']['requestBody'][0]);
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private static function getRequestBody(array $overrides = []): array
    {
        return array_merge([
            'stringProperty' => 'string',
            'numberProperty' => 0.1,
            'integerProperty' => 1,
            'booleanProperty' => true,
            'enumStringProperty' => 'def',
            'enumNullableStringProperty' => null,
            'integerRangeProperty' => 0,
            'emailProperty' => 'erwin.schrödinger@zol.fr',
            'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
            'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
            'dateTimeProperty2' => '1969-07-21T03:56:20.001+01:00',
            'dateTimeProperty3' => '1969-07-21T03:56:20Z',
            'dateTimeProperty4' => '1969-07-21T03:56:20.001Z',
            'dateProperty' => '1969-07-21',
            'timeProperty' => '03:56:20+01:00',
            'timeProperty2' => '03:56:20.001+01:00',
            'timeProperty3' => '03:56:20Z',
            'timeProperty4' => '03:56:20.001Z',
            'customProperty' => 'custom',
            'overriddenProperty' => 'abc',
            'objectProperty' => [
                'stringProperty' => 'string',
            ],
            'arrayProperty' => ['string'],
            'integerMatrixProperty' => [[1, 2], [3]],
            'objectArrayProperty' => [
                ['stringProperty' => 'string'],
            ],
            'recursiveObjectArray' => [
                [
                    'stringProperty' => 'string',
                    'numberProperty' => 0.1,
                    'integerProperty' => 1,
                    'booleanProperty' => true,
                    'enumStringProperty' => 'def',
                    'enumNullableStringProperty' => null,
                    'integerRangeProperty' => 0,
                    'emailProperty' => 'erwin.schrödinger@zol.fr',
                    'uuidProperty' => '83b23b90-9501-4da7-b35c-25134bdc45f8',
                    'dateTimeProperty' => '1969-07-21T03:56:20+01:00',
                    'dateTimeProperty2' => '1969-07-21T03:56:20.001+01:00',
                    'dateTimeProperty3' => '1969-07-21T03:56:20Z',
                    'dateTimeProperty4' => '1969-07-21T03:56:20.001Z',
                    'dateProperty' => '1969-07-21',
                    'timeProperty' => '03:56:20+01:00',
                    'timeProperty2' => '03:56:20.001+01:00',
                    'timeProperty3' => '03:56:20Z',
                    'timeProperty4' => '03:56:20.001Z',
                    'customProperty' => 'custom',
                    'overriddenProperty' => 'abc',
                    'objectProperty' => [
                        'stringProperty' => 'string',
                    ],
                    'arrayProperty' => ['string'],
                    'integerMatrixProperty' => [[1, 2], [3]],
                    'objectArrayProperty' => [
                        ['stringProperty' => 'string'],
                    ],
                    'recursiveObjectArray' => [],
                ],
            ],
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $requestBody
     */
    private static function createClientForQuery(string $query, array $requestBody): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $httpClient = self::createClient();
        $httpClient->catchExceptions(false);

        $httpClient->getCookieJar()->set(new Cookie('cookieParamString', 'string'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamNumber', '0.1'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamInteger', '1'));
        $httpClient->getCookieJar()->set(new Cookie('cookieParamBoolean', 'true'));

        $httpClient->jsonRequest(
            method: 'POST',
            uri: "/path/string/0.1/1/true{$query}",
            parameters: $requestBody,
            server: [
                'HTTP_HEADERPARAMSTRING' => 'string',
                'HTTP_HEADERPARAMNUMBER' => '0.1',
                'HTTP_HEADERPARAMINTEGER' => '1',
                'HTTP_HEADERPARAMBOOLEAN' => 'true',
            ],
        );

        return $httpClient;
    }

    /**
     * Denormalization failures of array and object query parameters must land in the
     * validation_failed envelope, never escape as a framework BadRequestException.
     */
    public function testC(): void
    {
        $httpClient = self::createClientForQuery(
            '?queryParamString=string&queryParamNumber=0.1&queryParamInteger=1&queryParamBoolean=true'
                .'&queryParamStringArray=aa'
                .'&queryParamIntegerMatrix[0][]=abc'
                .'&queryParamAbcList[0][]=x'
                .'&queryParamOptionalArray[3]=x'
                .'&queryParamAbcRef[def]=z'
                .'&queryParamNodeTree[name]=root',
            self::getRequestBody(),
        );

        $rawContent = $httpClient->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400);
        self::assertIsString($rawContent);
        $content = json_decode($rawContent, true);
        self::assertIsArray($content);

        self::assertEqualsCanonicalizing(
            [
                'code' => 'validation_failed',
                'message' => 'Validation has failed.',
                'errors' => [
                    'query' => [
                        'queryParamStringArray' => ["Parameter 'queryParamStringArray' in 'query' must be an array."],
                        'queryParamIntegerMatrix' => ["Parameter 'queryParamIntegerMatrix[0][0]' in 'query' must be an integer."],
                        'queryParamAbcList' => ["Parameter 'queryParamAbcList[0][def]' in 'query' is required."],
                        'queryParamObject' => ["Parameter 'queryParamObject' in 'query' is required."],
                        'queryParamOptionalArray' => ["Parameter 'queryParamOptionalArray' in 'query' must be a list."],
                    ],
                ],
            ],
            $content,
        );
    }

    /**
     * Constraint violations nested in an array or an object query parameter must carry their
     * location.
     */
    public function testD(): void
    {
        $httpClient = self::createClientForQuery(
            '?queryParamString=string&queryParamNumber=0.1&queryParamInteger=1&queryParamBoolean=true'
                .'&queryParamStringArray[]=a'
                .'&queryParamIntegerMatrix[0][]=1'
                .'&queryParamAbcList[0][def]=x'
                .'&queryParamObject[stringProperty]=s&queryParamObject[nestedArrayProperty][]=1'
                .'&queryParamObject[nestedObjectProperty][emailProperty]=notanemail'
                .'&queryParamAbcRef[def]=z'
                .'&queryParamNodeTree[name]=root',
            self::getRequestBody(),
        );

        $rawContent = $httpClient->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400);
        self::assertIsString($rawContent);
        $content = json_decode($rawContent, true);
        self::assertIsArray($content);

        self::assertEqualsCanonicalizing(
            [
                'code' => 'validation_failed',
                'message' => 'Validation has failed.',
                'errors' => [
                    'query' => [
                        'queryParamStringArray' => ['[0]: This value is too short. It should have 2 characters or more.'],
                        'queryParamObject' => ['nestedObjectProperty.emailProperty: This value is not a valid email address.'],
                    ],
                ],
            ],
            $content,
        );
    }
}
