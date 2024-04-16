<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentTranslationDataVersion;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Pagination;


class GetContentTranslationDataVersions200ApplicationJsonResponsePayload
{
    /**
     * @param array<ContentTranslationDataVersion> $data
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
