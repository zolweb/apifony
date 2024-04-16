<?php

namespace Zol\TestOpenApiServer\Api\MediaTree;

use Symfony\Component\Validator\Constraints as Assert;


class UpdateMediaFolderApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
