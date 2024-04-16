<?php

namespace Zol\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;


class MediaFolder
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertMediaFolderId]
        public readonly string $id,

        #[Assert\NotNull]
        public readonly string $name,

        #[Assert\NotNull]
        #[AssertMediaFolderId]
        public readonly string $folderId,

        #[Assert\NotNull]
        public readonly int $creationTimestamp,
    ) {
    }
}
