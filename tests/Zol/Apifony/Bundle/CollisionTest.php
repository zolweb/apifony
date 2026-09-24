<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony\Bundle;

use PHPUnit\Framework\TestCase;
use Zol\Apifony\Bundle\Bundle;
use Zol\Apifony\Bundle\Exception as BundleException;
use Zol\Apifony\OpenApi\Exception as OpenApiException;
use Zol\Apifony\OpenApi\OpenApi;

/**
 * Turning a specification name into a PHP identifier drops everything that is neither a letter nor
 * a digit, so distinct names can collapse onto one. Nothing used to cover what the generator does
 * about it: the fixture cannot carry a collision, since one aborts generation before the suite
 * runs.
 *
 * @internal
 *
 * @coversNothing
 */
final class CollisionTest extends TestCase
{
    /**
     * @param array<mixed> $spec
     *
     * @dataProvider provideACollapsingNameIsRefusedCases
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testACollapsingNameIsRefused(array $spec, string $message, string $bundleName = 'TestBundle'): void
    {
        $this->expectException(BundleException::class);
        $this->expectExceptionMessage($message);

        self::build($spec, $bundleName);
    }

    /**
     * @return iterable<string, array{array<mixed>, string}|array{array<mixed>, string, string}>
     */
    public static function provideACollapsingNameIsRefusedCases(): iterable
    {
        yield 'two operations on one aggregate' => [
            ['paths' => [
                '/a' => ['get' => ['operationId' => 'getUser']],
                '/b' => ['get' => ['operationId' => 'get_user']],
            ]],
            'Operation \'getUser\' and operation \'get_user\' both map to the aggregate \'GetUser\'.',
        ];

        yield 'two parameters on one handler argument' => [
            ['paths' => ['/a' => ['get' => [
                'operationId' => 'op',
                'parameters' => [
                    ['name' => 'a-b', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string']],
                    ['name' => 'a_b', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string']],
                ],
            ]]]],
            'Parameter \'a-b\' and parameter \'a_b\' both map to the handler argument \'$qAB\'.',
        ];

        yield 'two schemas on one model' => [
            ['components' => ['schemas' => [
                'User' => self::objectSchema(),
                'user' => self::objectSchema(),
            ]]],
            'Schema \'User\' and schema \'user\' both map to the class',
        ];

        // Naming preserves inner case, so these two produce 'ABC' and 'Abc': different strings, one
        // PHP class, one file on a case insensitive filesystem.
        yield 'two schemas differing only in case' => [
            ['components' => ['schemas' => [
                'a_b_c' => self::objectSchema(),
                'abc' => self::objectSchema(),
            ]]],
            'both map to the class',
        ];

        // Two models may legitimately share a class name in different namespaces, since an inline
        // model is named after the operation it belongs to. What cannot happen is both being
        // denormalized: the controller they share would carry one method name for two models.
        yield 'two models on one denormalizer' => [
            [
                'components' => ['schemas' => ['op_node' => self::objectSchema()]],
                'paths' => ['/a' => ['get' => [
                    'operationId' => 'op',
                    'parameters' => [
                        ['name' => 'node', 'in' => 'query', 'required' => true, 'schema' => self::objectSchema()],
                        ['name' => 'ref', 'in' => 'query', 'required' => true, 'schema' => ['$ref' => '#/components/schemas/op_node']],
                    ],
                ]]],
            ],
            'both map to the method',
        ];

        // Only one of the two is denormalized here, so the clash surfaces where the file would
        // import a short name that means two different classes.
        yield 'two models on one import' => [
            [
                'components' => ['schemas' => ['op_node' => self::objectSchema()]],
                'paths' => ['/a' => ['get' => [
                    'operationId' => 'op',
                    'parameters' => [
                        ['name' => 'node', 'in' => 'query', 'required' => true, 'schema' => self::objectSchema()],
                    ],
                ]]],
            ],
            'both map to the import',
        ];

        // Not a missing file but a broken bundle: the controller extends an AbstractController
        // declaring validate(mixed, string, array): void, so this emits an incompatible override
        // and fails when the class is loaded.
        yield 'an operation named after a method the controller inherits' => [
            ['paths' => ['/a' => ['get' => ['operationId' => 'validate']]]],
            'both map to the method',
        ];

        yield 'two response headers on one constructor parameter' => [
            ['paths' => ['/a' => ['get' => [
                'operationId' => 'op',
                'responses' => [200 => ['headers' => [
                    'x-rate-limit' => ['required' => true, 'schema' => ['type' => 'string']],
                    'X_Rate_Limit' => ['required' => true, 'schema' => ['type' => 'string']],
                ]]],
            ]]]],
            'both map to the property',
        ];

        yield 'a response header colliding with the payload parameter' => [
            ['paths' => ['/a' => ['get' => [
                'operationId' => 'op',
                'responses' => [200 => [
                    'headers' => ['payload' => ['required' => true, 'schema' => ['type' => 'string']]],
                    'content' => ['application/json' => ['schema' => self::objectSchema()]],
                ]],
            ]]]],
            'both map to the property',
        ];

        yield 'two formats on one constraint class' => [
            ['components' => ['schemas' => ['A' => [
                'type' => 'object',
                'properties' => [
                    'x' => ['type' => 'string', 'format' => 'date-time'],
                    'y' => ['type' => 'string', 'format' => 'dateTime'],
                ],
                'required' => ['x', 'y'],
            ]]]],
            'Format \'date-time\' and format \'dateTime\' both map to the class',
        ];
    }

    /**
     * @param array<mixed> $spec
     *
     * @dataProvider provideANameThatCannotBecomeAnIdentifierIsRefusedCases
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testANameThatCannotBecomeAnIdentifierIsRefused(array $spec, string $message, string $bundleName = 'TestBundle'): void
    {
        $this->expectException(BundleException::class);
        $this->expectExceptionMessage($message);

        self::build($spec, $bundleName);
    }

    /**
     * @return iterable<string, array{array<mixed>, string}|array{array<mixed>, string, string}>
     */
    public static function provideANameThatCannotBecomeAnIdentifierIsRefusedCases(): iterable
    {
        yield 'an operationId starting with a digit' => [
            ['paths' => ['/a' => ['get' => ['operationId' => '2fa']]]],
            'Operation \'2fa\' produces \'2fa\', which is not a valid PHP identifier.',
        ];

        // A path parameter is the one name used verbatim, because the router injects it into the
        // controller by name and it cannot be normalized away.
        yield 'a path parameter carrying a dash' => [
            ['paths' => ['/a/{user-id}' => ['get' => [
                'operationId' => 'op',
                'parameters' => [['name' => 'user-id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
            ]]]],
            'Path parameter \'user-id\' produces \'user-id\', which is not a valid PHP identifier.',
        ];

        yield 'a model property starting with a digit' => [
            ['components' => ['schemas' => ['A' => [
                'type' => 'object',
                'properties' => ['0foo' => ['type' => 'string']],
                'required' => ['0foo'],
            ]]]],
            'Property \'0foo\' produces \'0foo\', which is not a valid PHP identifier.',
        ];

        yield 'a bundle name made only of punctuation' => [
            ['paths' => []],
            'Bundle name \'!!!\' produces \'\', which is not a valid PHP identifier.',
            '!!!',
        ];
    }

    /**
     * One schema reached from several places, and one reaching itself, claim their name more than
     * once. Neither is a collision, and confusing the two would refuse every specification that
     * shares or recurses.
     *
     * @param array<mixed> $spec
     *
     * @dataProvider provideOneSchemaReachedTwiceIsNotACollisionCases
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testOneSchemaReachedTwiceIsNotACollision(array $spec): void
    {
        self::build($spec, 'TestBundle');

        $this->expectNotToPerformAssertions();
    }

    /**
     * @return iterable<string, array{array<mixed>}>
     */
    public static function provideOneSchemaReachedTwiceIsNotACollisionCases(): iterable
    {
        $ref = ['$ref' => '#/components/schemas/Abc'];

        yield 'a component referenced from two properties' => [
            ['components' => ['schemas' => [
                'Abc' => self::objectSchema(),
                'Holder' => ['type' => 'object', 'properties' => ['a' => $ref, 'b' => $ref], 'required' => ['a', 'b']],
            ]]],
        ];

        yield 'a component referenced through an array' => [
            ['components' => ['schemas' => [
                'Abc' => self::objectSchema(),
                'Holder' => ['type' => 'object', 'properties' => ['a' => ['type' => 'array', 'items' => $ref]], 'required' => ['a']],
            ]]],
        ];

        yield 'a schema recursing through itself' => [
            ['components' => ['schemas' => [
                'Node' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'children' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Node'], 'default' => []],
                    ],
                    'required' => ['name'],
                ],
            ]]],
        ];

        // Unwrapping an array level keeps the name the model would take and changes the location it
        // is read at, which must not read as a second claimant.
        yield 'an inline array of objects' => [
            ['components' => ['schemas' => [
                'Holder' => [
                    'type' => 'object',
                    'properties' => ['a' => ['type' => 'array', 'items' => self::objectSchema()]],
                    'required' => ['a'],
                ],
            ]]],
        ];
    }

    /**
     * @return array<mixed>
     */
    private static function objectSchema(): array
    {
        return ['type' => 'object', 'properties' => ['x' => ['type' => 'string']], 'required' => ['x']];
    }

    /**
     * @param array<mixed> $spec
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    private static function build(array $spec, string $bundleName): void
    {
        Bundle::build($bundleName, 'zol/probe', 'Ns', OpenApi::build($spec));
    }
}
