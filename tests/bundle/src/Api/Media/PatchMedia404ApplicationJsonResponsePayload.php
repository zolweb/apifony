<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;


class PatchMedia404ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $code,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
