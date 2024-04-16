<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

use Symfony\Component\Validator\Constraints as Assert;


class PostContent400ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $code,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
