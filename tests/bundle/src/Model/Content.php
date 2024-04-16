<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Content
{
    /**
     * @param array<ContentTranslation> $translations
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,

        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        public readonly string $contentTypeId,

        #[Assert\NotNull]
        public readonly string $name,

        #[Assert\NotNull]
        public readonly string $status,

        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $translations,

        #[Assert\NotNull]
        public readonly int $creationTimestamp,
    ) {
    }
}
