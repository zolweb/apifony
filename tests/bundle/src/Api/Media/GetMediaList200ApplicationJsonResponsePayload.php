<?php

namespace Zol\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Model\Media;
use Zol\TestOpenApiServer\Model\Pagination;


class GetMediaList200ApplicationJsonResponsePayload
{
    /**
     * @param array<Media> $data
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
