<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;
class Media
{
    /**
     * @param array<MediaDescription> $description
     */
    public function __construct(#[Assert\NotNull] public readonly string $id, #[Assert\NotNull] public readonly int $version, #[Assert\NotNull] public readonly string $name, #[Assert\NotNull] #[AssertMediaFolderId] public readonly string $folderId, #[Assert\NotNull] public readonly string $url, #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $description, #[Assert\NotNull] public readonly string $credit, #[Assert\NotNull] public readonly string $type, #[Assert\NotNull] public readonly int $size, #[Assert\NotNull] public readonly int $creationTimestamp)
    {
    }
}