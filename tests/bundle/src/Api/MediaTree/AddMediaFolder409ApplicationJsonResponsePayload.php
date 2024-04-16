<?php

namespace Zol\TestOpenApiServer\Api\MediaTree;

use Symfony\Component\Validator\Constraints as Assert;


class AddMediaFolder409ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $code,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
