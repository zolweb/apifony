<?php

namespace Zol\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;


class MediaTree
{
    /**
     * @param array<MediaFolderRecursive> $mediaFolders
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,

        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $mediaFolders,
    ) {
    }
}
