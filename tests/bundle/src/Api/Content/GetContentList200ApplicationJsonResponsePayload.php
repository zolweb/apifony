<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Content;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Pagination;


class GetContentList200ApplicationJsonResponsePayload
{
    /**
     * @param array<Content> $data
     */
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $data,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly Pagination $pagination,
    ) {
    }
}
