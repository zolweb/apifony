<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

use Symfony\Component\Validator\Constraints as Assert;


class ArchiveContentApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,
    ) {
    }
}
