<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony\Bundle;

use PHPUnit\Framework\TestCase;
use Zol\Apifony\Bundle\Bundle;
use Zol\Apifony\Bundle\Exception as BundleException;
use Zol\Apifony\OpenApi\Exception as OpenApiException;
use Zol\Apifony\OpenApi\OpenApi;

/**
 * @internal
 *
 * @coversNothing
 */
final class IgnoredOperationTest extends TestCase
{
    /**
     * An operation marked x-apifony-ignore is left out while the document is resolved, so nothing
     * further down has to remember it was there. It used to be skipped only where the controllers
     * are built, which meant a format mentioned by nothing else still got a constraint, a
     * validator and a definition interface emitted for it, plus its service wiring.
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testAnIgnoredOperationEmitsNothing(): void
    {
        $paths = ['/a' => ['get' => [
            'operationId' => 'ignoredOperation',
            'x-apifony-ignore' => true,
            'responses' => [200 => ['content' => ['application/json' => ['schema' => [
                'type' => 'object',
                'properties' => ['stamp' => ['type' => 'string', 'format' => 'ignored-format']],
                'required' => ['stamp'],
            ]]]]],
        ]]];

        self::assertSame(
            ['composer.json', 'config/routes.yaml', 'config/services.yaml', 'src/Api/AbstractController.php', 'src/Api/ConstraintValidatorFactory.php', 'src/Api/DenormalizationException.php', 'src/Api/ValidationException.php', 'src/NsBundle.php'],
            self::getGeneratedPaths($paths),
        );
    }

    /**
     * The same operation without the marker, so that the previous assertion is about the marker
     * rather than about the shape of the probe.
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testTheSameOperationKeptEmitsItsFormat(): void
    {
        $paths = ['/a' => ['get' => [
            'operationId' => 'ignoredOperation',
            'responses' => [200 => ['content' => ['application/json' => ['schema' => [
                'type' => 'object',
                'properties' => ['stamp' => ['type' => 'string', 'format' => 'ignored-format']],
                'required' => ['stamp'],
            ]]]]],
        ]]];

        $paths = self::getGeneratedPaths($paths);

        self::assertContains('src/Format/IgnoredFormat.php', $paths);
        self::assertContains('src/Format/IgnoredFormatValidator.php', $paths);
        self::assertContains('src/Format/IgnoredFormatDefinition.php', $paths);
    }

    /**
     * @param array<mixed> $paths
     *
     * @return list<string>
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    private static function getGeneratedPaths(array $paths): array
    {
        $bundle = Bundle::build('Ns', 'zol/probe', 'Ns', OpenApi::build(['paths' => $paths]));

        $generated = [];
        foreach ($bundle->getFiles() as $file) {
            $generated[] = $file->getFolder() === '' ? $file->getName() : "{$file->getFolder()}/{$file->getName()}";
        }
        sort($generated);

        return $generated;
    }
}
