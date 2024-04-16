<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Symfony\Component\Validator\Constraints as Assert;


class PostContentTranslationApplicationJsonRequestBodyPayload
{
    /**
     * @param array<mixed>|\stdClass $data
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $contentId,

        #[Assert\NotNull]
        public readonly string $localeId,

        #[Assert\NotNull]
        public readonly array|\stdClass $data,
    ) {
    }
}
