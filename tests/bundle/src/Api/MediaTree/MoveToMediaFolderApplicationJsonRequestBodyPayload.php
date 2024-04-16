<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\MediaTree;

use Symfony\Component\Validator\Constraints as Assert;


class MoveToMediaFolderApplicationJsonRequestBodyPayload
{
    /**
     * @param array<string> $movedMediaIds
     * @param array<string> $movedMediaFolderIds
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $movedMediaIds,

        #[Assert\NotNull]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $movedMediaFolderIds,

        #[Assert\NotNull]
        public readonly string $destinationMediaFolderId,
    ) {
    }
}
