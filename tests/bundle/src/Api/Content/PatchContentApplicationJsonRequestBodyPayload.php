<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

use Symfony\Component\Validator\Constraints as Assert;


class PatchContentApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
