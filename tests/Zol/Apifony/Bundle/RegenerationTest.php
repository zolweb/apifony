<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests\Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Zol\Apifony\Bundle\AbstractController;
use Zol\Apifony\Bundle\Bundle;
use Zol\Apifony\Bundle\Exception as BundleException;
use Zol\Apifony\Bundle\File;
use Zol\Apifony\OpenApi\Exception as OpenApiException;
use Zol\Apifony\OpenApi\OpenApi;

/**
 * The bundle committed under tests/bundle is the generator's own output for tests/openapi.yaml,
 * which makes it a snapshot: any change to what the generator emits shows up as a diff there.
 *
 * Nothing used to read that snapshot back. It was checked by regenerating over it and looking at
 * `git diff`, which only holds as long as someone remembers to do it, and which the CI workflow
 * does not do at all. This asserts it instead, in memory, without the command and without touching
 * the working tree.
 *
 * @internal
 *
 * @coversNothing
 */
final class RegenerationTest extends TestCase
{
    private const BUNDLE_NAME = 'TestOpenApiServer';
    private const PACKAGE_NAME = 'zol/test-openapi-server';
    private const NAMESPACE = 'Zol\Apifony\Tests\TestOpenApiServer';

    /**
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testEveryGeneratedFileMatchesTheCommittedBundle(): void
    {
        self::skipUnlessThePrinterMatchesTheSnapshot();

        foreach ($this->getGeneratedFiles() as $path => $file) {
            self::assertFileExists(
                self::getBundleDir()."/{$path}",
                \sprintf('The generator emits \'%s\', which the committed bundle does not hold.', $path),
            );
            self::assertStringEqualsFile(
                self::getBundleDir()."/{$path}",
                $file->getContent(),
                \sprintf('The content generated for \'%s\' differs from the committed one.', $path),
            );
        }
    }

    /**
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testTheCommittedBundleHoldsNothingTheGeneratorNoLongerEmits(): void
    {
        self::assertEqualsCanonicalizing(
            array_keys($this->getGeneratedFiles()),
            $this->getCommittedPaths(),
        );
    }

    /**
     * @throws BundleException
     * @throws OpenApiException
     */
    public function testNoTwoGeneratedFilesShareAPath(): void
    {
        $paths = [];
        foreach (self::buildBundle()->getFiles() as $file) {
            $paths[] = self::getPath($file);
        }

        self::assertSame(array_unique($paths), $paths);
    }

    /**
     * The registry seeds the AbstractController's own method names so that an operation landing on
     * one of them is refused rather than emitting an incompatible override. That list is written
     * by hand, so it is the one thing here that can quietly drift away from what the class really
     * declares. Read the emitted file back and let it say.
     */
    public function testEveryMethodTheAbstractControllerDeclaresIsAccountedFor(): void
    {
        $ast = (new ParserFactory())->createForHostVersion()->parse(
            (string) file_get_contents(self::getBundleDir().'/src/Api/AbstractController.php'),
        );

        self::assertNotNull($ast);

        $declared = [];
        foreach ((new NodeFinder())->findInstanceOf($ast, ClassMethod::class) as $node) {
            $name = self::getMethodName($node);
            if ($name !== null) {
                $declared[] = $name;
            }
        }

        self::assertNotEmpty($declared);

        $fixed = AbstractController::getFixedMethodNames();
        foreach ($declared as $name) {
            // The denormalizers are named after the models, so they are claimed as they are emitted
            // rather than seeded.
            if (preg_match('/^denormalize.+Value$/', $name) === 1) {
                continue;
            }

            self::assertContains($name, $fixed, \sprintf('\'%s\' is declared but not among the names the registry reserves.', $name));
        }
    }

    /**
     * @return array<string, File>
     *
     * @throws BundleException
     * @throws OpenApiException
     */
    private function getGeneratedFiles(): array
    {
        $files = [];
        foreach (self::buildBundle()->getFiles() as $file) {
            $files[self::getPath($file)] = $file;
        }

        return $files;
    }

    /**
     * Taken through a Node rather than read off the match directly: php-parser only carries the
     * narrowed element type from v5 on, and the project supports v4 too.
     */
    private static function getMethodName(Node $node): ?string
    {
        return $node instanceof ClassMethod ? $node->name->toString() : null;
    }

    /**
     * The committed bundle is what one php-parser version printed, and the printer changed its
     * spacing around return types between v4 and v5. Comparing content against the snapshot only
     * means something when the printer at hand is the one that produced it, so this asks the
     * printer rather than guessing from a version number.
     *
     * The other assertions here are about which files exist and what they declare, which no
     * printer changes, so they run either way.
     */
    private static function skipUnlessThePrinterMatchesTheSnapshot(): void
    {
        $probe = (new Standard())->prettyPrint([
            (new BuilderFactory())->method('probe')->makePublic()->setReturnType('void')->getNode(),
        ]);

        if (!str_contains($probe, '): void')) {
            self::markTestSkipped('The php-parser in use does not print what the committed bundle was printed with.');
        }
    }

    /**
     * Every file git tracks under tests/bundle, so that a file the generator stops emitting fails
     * here rather than lingering unnoticed in a working tree nobody cleaned.
     *
     * @return list<string>
     */
    private function getCommittedPaths(): array
    {
        $command = \sprintf('git -C %s ls-files --cached -- tests/bundle', escapeshellarg(\dirname(self::getBundleDir(), 2)));

        $output = [];
        $status = 0;
        exec($command, $output, $status);

        self::assertSame(0, $status, 'Listing the committed bundle files failed.');
        self::assertNotEmpty($output, 'No bundle file is tracked, so there is nothing to compare against.');

        $paths = [];
        foreach ($output as $line) {
            $paths[] = substr($line, \strlen('tests/bundle/'));
        }

        return $paths;
    }

    /**
     * @throws BundleException
     * @throws OpenApiException
     */
    private static function buildBundle(): Bundle
    {
        $spec = Yaml::parseFile(\dirname(self::getBundleDir()).'/openapi.yaml');

        self::assertIsArray($spec);

        return Bundle::build(
            self::BUNDLE_NAME,
            self::PACKAGE_NAME,
            self::NAMESPACE,
            OpenApi::build($spec),
        );
    }

    /**
     * The path the command would write the file to. A folder is empty for the files sitting at the
     * bundle root, so the separator cannot simply be concatenated.
     */
    private static function getPath(File $file): string
    {
        $folder = $file->getFolder();

        return $folder === '' ? $file->getName() : "{$folder}/{$file->getName()}";
    }

    private static function getBundleDir(): string
    {
        return \dirname(__DIR__, 3).'/bundle';
    }
}
