<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Symfony\Component\Validator\Constraints as Assert;


class PostContentTranslation409ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $code,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
