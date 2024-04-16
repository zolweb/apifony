<?php

namespace Zol\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;


class ArchiveMedia409ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $code,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
