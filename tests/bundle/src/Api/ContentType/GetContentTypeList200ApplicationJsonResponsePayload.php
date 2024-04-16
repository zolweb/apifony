<?php

namespace Zol\TestOpenApiServer\Api\ContentType;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Model\ContentType;


class GetContentTypeList200ApplicationJsonResponsePayload
{
    /**
     * @param array<ContentType> $data
     */
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $data,
    ) {
    }
}
