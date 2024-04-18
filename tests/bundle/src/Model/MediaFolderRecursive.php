<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;
class MediaFolderRecursive
{
    /**
     * @param array<MediaFolderRecursive> $mediaFolders
     */
    public function __construct(#[Assert\NotNull] #[AssertMediaFolderId] public readonly string $id, #[Assert\NotNull] public readonly string $name, #[Assert\NotNull] #[AssertMediaFolderId] public readonly string $folderId, #[Assert\NotNull] public readonly int $creationTimestamp, #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $mediaFolders)
    {
    }
}