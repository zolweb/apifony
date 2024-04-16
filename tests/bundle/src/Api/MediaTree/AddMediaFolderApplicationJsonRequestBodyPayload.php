<?php

namespace Zol\TestOpenApiServer\Api\MediaTree;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;


class AddMediaFolderApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        public readonly string $name,

        #[AssertMediaFolderId]
        public readonly ?string $folderId,
    ) {
    }
}
