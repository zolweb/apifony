<?php

namespace Zol\TestOpenApiServer\Api\MediaTree;

use Symfony\Component\Validator\Constraints as Assert;


class DeleteMediaFolderApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,
    ) {
    }
}
