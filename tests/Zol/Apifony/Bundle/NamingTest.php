<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony\Bundle;

use PHPUnit\Framework\TestCase;
use Zol\Apifony\Bundle\Naming;

/**
 * @internal
 *
 * @coversNothing
 */
final class NamingTest extends TestCase
{
    /**
     * Several places build a class name out of either a components name or a name synthesized from
     * where the schema sits, and normalize the result once. That is only the same thing as
     * normalizing each branch separately because the conversion is idempotent, and nothing but
     * this says so: the fixture cannot catch a second application, since the names it carries are
     * already in the shape the conversion produces.
     *
     * @dataProvider provideConvertingAClassNameTwiceChangesNothingCases
     */
    public function testConvertingAClassNameTwiceChangesNothing(string $rawName): void
    {
        $once = Naming::forClass($rawName);

        self::assertSame($once, Naming::forClass($once));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideConvertingAClassNameTwiceChangesNothingCases(): iterable
    {
        $names = [
            // Shapes the generator synthesizes.
            'Schema_objectProperty',
            'FirstOperation_queryParamObject',
            'firstOperation_200_ResponsePayload',
            'rawShapesOperation_RequestBodyPayload',
            // Shapes a specification is free to write.
            'Foo_Bar',
            'FooBar',
            'foo-bar',
            'date-time',
            'dateTime',
            'a_b_c',
            'ABC',
            'XMLHttpRequest',
            'user',
            'User',
            'Node',
            // Names the conversion cannot rescue, which still must not change on a second pass.
            '2fa',
            '',
        ];

        foreach ($names as $name) {
            yield ($name === '' ? '<empty>' : $name) => [$name];
        }
    }

    /**
     * The conversion is lossy, which is the property every name scope has to live with. Stating it
     * here keeps it from being rediscovered as a surprise.
     */
    public function testDistinctNamesCanCollapseOntoOne(): void
    {
        self::assertSame(Naming::forClass('a_b_c'), Naming::forClass('ABC'));
        self::assertSame(Naming::forClass('date-time'), Naming::forClass('dateTime'));
        self::assertSame(Naming::forClass('user'), Naming::forClass('User'));
    }

    public function testANameThatCannotBecomeAnIdentifierIsReported(): void
    {
        self::assertTrue(Naming::isIdentifier('Abc'));
        self::assertTrue(Naming::isIdentifier('_abc'));
        // PHP accepts bytes above 0x7F in an identifier, so an accented name is fine.
        self::assertTrue(Naming::isIdentifier('café'));
        self::assertFalse(Naming::isIdentifier('2fa'));
        self::assertFalse(Naming::isIdentifier(''));
        self::assertFalse(Naming::isIdentifier('a-b'));
    }
}
