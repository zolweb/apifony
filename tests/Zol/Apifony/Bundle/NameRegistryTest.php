<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony\Bundle;

use PHPUnit\Framework\TestCase;
use Zol\Apifony\Bundle\Exception;
use Zol\Apifony\Bundle\NameRegistry;
use Zol\Apifony\Bundle\Origin;

/**
 * @internal
 *
 * @coversNothing
 */
final class NameRegistryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAFreeNameIsGranted(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'Abc', self::schema('Abc'));

        $this->expectNotToPerformAssertions();
    }

    /**
     * A component schema reached from several places, and a recursive one reached through itself,
     * claim the same name more than once. They are one claim repeated, which the specification
     * location is what tells apart from a genuine clash.
     *
     * @throws Exception
     */
    public function testTheSameClaimantMayClaimTwice(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'Abc', self::schema('Abc'));
        $registry->claimClass('Ns\Model', 'Abc', self::schema('Abc'));

        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws Exception
     */
    public function testTwoClaimantsOnOneNameCollide(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'Abc', self::schema('Abc'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Schema \'Abc\' and schema \'abc\' both map to the class \'Ns\Model\Abc\'.');

        $registry->claimClass('Ns\Model', 'Abc', self::schema('abc'));
    }

    /**
     * The path reported is the incoming claimant's, which is the one the author has to go and fix.
     *
     * @throws Exception
     */
    public function testTheCollisionIsReportedAtTheSecondClaimant(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'Abc', self::schema('Abc'));

        try {
            $registry->claimClass('Ns\Model', 'Abc', self::schema('abc'));
            self::fail('The second claim was expected to be refused.');
        } catch (Exception $e) {
            self::assertSame(['documentation root', 'components', 'schemas', 'abc'], $e->path);
        }
    }

    /**
     * @throws Exception
     */
    public function testScopesDoNotSeeEachOther(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'Abc', self::schema('one'));
        $registry->claimAggregate('Abc', self::schema('two'));
        $registry->claimMethod('Ns\Api\AbstractController', 'Abc', self::schema('three'));

        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws Exception
     */
    public function testOneNamespaceDoesNotReachIntoAnother(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'Node', self::schema('one'));
        $registry->claimClass('Ns\Api\Op', 'Node', self::schema('two'));

        $this->expectNotToPerformAssertions();
    }

    /**
     * Neither PHP nor a case insensitive filesystem tells 'Abc' from 'ABC', so the registry must
     * not either.
     *
     * @throws Exception
     */
    public function testClassNamesAreComparedWithoutCase(): void
    {
        $registry = new NameRegistry();
        $registry->claimClass('Ns\Model', 'ABC', self::schema('a_b_c'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('both map to the class \'Ns\Model\ABC\'');

        $registry->claimClass('Ns\Model', 'Abc', self::schema('abc'));
    }

    /**
     * A variable name is case sensitive, so two spellings are two arguments.
     *
     * @throws Exception
     */
    public function testArgumentNamesAreComparedWithCase(): void
    {
        $registry = new NameRegistry();
        $registry->claimArgument('Ns\Api\Op\OpHandler', 'op', 'qAbc', self::schema('one'));
        $registry->claimArgument('Ns\Api\Op\OpHandler', 'op', 'qABC', self::schema('two'));

        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws Exception
     */
    public function testTwoParametersOnOneArgumentCollide(): void
    {
        $registry = new NameRegistry();
        $registry->claimArgument('Ns\Api\Op\OpHandler', 'op', 'qAB', self::parameter('a-b'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Parameter \'a-b\' and parameter \'a_b\' both map to the handler argument \'$qAB\'.');

        $registry->claimArgument('Ns\Api\Op\OpHandler', 'op', 'qAB', self::parameter('a_b'));
    }

    /**
     * @throws Exception
     */
    public function testANameThatCannotBecomeAnIdentifierIsRefused(): void
    {
        $registry = new NameRegistry();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Operation \'2fa\' produces \'2fa\', which is not a valid PHP identifier.');

        $registry->claimAggregate('2fa', Origin::spec('operation', '2fa', ['documentation root']));
    }

    /**
     * @throws Exception
     */
    public function testANameLeftEmptyByTheConversionIsRefused(): void
    {
        $registry = new NameRegistry();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Bundle name \'!!!\' produces \'\', which is not a valid PHP identifier.');

        $registry->claimBundle('', Origin::spec('bundle name', '!!!', ['documentation root']));
    }

    private static function schema(string $rawName): Origin
    {
        return Origin::spec('schema', $rawName, ['documentation root', 'components', 'schemas', $rawName]);
    }

    private static function parameter(string $rawName): Origin
    {
        return Origin::spec('parameter', $rawName, ['documentation root', 'paths', '/a', 'get', 'parameters', $rawName]);
    }
}
