<?php

namespace Zol\TestOpenApiServer\Api\Content;

use Symfony\Component\Validator\Constraints as Assert;


class PostContentApplicationJsonRequestBodyPayload
{
    /**
     * @param array<mixed>|\stdClass $data
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $contentTypeId,

        #[Assert\NotNull]
        public readonly string $name,

        #[Assert\NotNull]
        public readonly string $localeId,

        #[Assert\NotNull]
        public readonly array|\stdClass $data,
    ) {
    }
}
