<?php

namespace Zol\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;


class ContentTranslation
{
    /**
     * @param array<mixed>|\stdClass $data
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,

        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        public readonly string $contentId,

        #[Assert\NotNull]
        public readonly string $localeId,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
            'a',
            'b',
        ])]
        public readonly string $status,

        #[Assert\NotNull]
        public readonly array|\stdClass $data,

        #[Assert\NotNull]
        public readonly int $creationTimestamp,
    ) {
    }
}
