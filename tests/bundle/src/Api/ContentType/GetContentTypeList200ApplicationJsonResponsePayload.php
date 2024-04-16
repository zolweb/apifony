<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentType;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentType;


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
