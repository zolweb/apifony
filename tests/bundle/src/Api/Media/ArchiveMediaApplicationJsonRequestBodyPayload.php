<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;


class ArchiveMediaApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,
    ) {
    }
}
