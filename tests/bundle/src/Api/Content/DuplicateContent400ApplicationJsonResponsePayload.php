<?php

namespace Zol\TestOpenApiServer\Api\Content;

use Symfony\Component\Validator\Constraints as Assert;


class DuplicateContent400ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $code,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
