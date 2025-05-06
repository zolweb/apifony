<?php

declare(strict_types=1);

namespace Zol\Ogen\OpenApi;

class OpenApi
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $path = ['documentation root'];

        if (isset($data['components']) && !\is_array($data['components'])) {
            throw new Exception('OpenApi object components attribute must be an array.', $path);
        }
        if (isset($data['paths']) && !\is_array($data['paths'])) {
            throw new Exception('OpenApi object paths attribute must be an array.', $path);
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        $componentsPath = $path;
        $componentsPath[] = 'components';
        $pathsPath = $path;
        $pathsPath[] = 'paths';

        return new self(
            $components = isset($data['components']) ? Components::build($data['components'], $componentsPath) : null,
            isset($data['paths']) ? Paths::build($data['paths'], $components, $pathsPath) : null,
            $extensions,
            $path,
        );
    }

    /**
     * @param array<string, mixed> $extensions
     * @param list<string>         $path
     */
    private function __construct(
        public readonly ?Components $components,
        public readonly ?Paths $paths,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
