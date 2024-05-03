<?php

declare(strict_types=1);

namespace Zol\Ogen;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;
use Zol\Ogen\Bundle\Bundle;
use Zol\Ogen\Bundle\Exception as BundleException;
use Zol\Ogen\OpenApi\Exception as OpenApiException;
use Zol\Ogen\OpenApi\OpenApi;

class Ogen
{
    /**
     * @throws OpenApiException
     * @throws BundleException
     */
    public function generate(string $bundleName, string $namespace, string $packageName, string $openApiSpecPath, string $outputDir): void
    {
        $spec = Yaml::parseFile($openApiSpecPath);

        if (!\is_array($spec)) {
            throw new \RuntimeException('Yaml content must be an array');
        }

        $openApi = OpenApi::build($spec);
        $bundle = Bundle::build($bundleName, $packageName, $namespace, $openApi);

        foreach ($bundle->getFiles() as $file) {
            if (!file_exists("{$outputDir}/{$file->getFolder()}")) {
                mkdir("{$outputDir}/{$file->getFolder()}", recursive: true);
            }

            file_put_contents("{$outputDir}/{$file->getFolder()}/{$file->getName()}", $file->getContent());
        }
    }
}
