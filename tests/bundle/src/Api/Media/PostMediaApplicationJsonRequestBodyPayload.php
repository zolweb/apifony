<?php

namespace Zol\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;


class PostMediaApplicationJsonRequestBodyPayload
{
    /**
     * @param array<PostMediaApplicationJsonRequestBodyPayloadDescription> $description
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $name,

        #[Assert\NotNull]
        #[AssertMediaFolderId]
        public readonly string $folderId,

        #[Assert\NotNull]
        public readonly string $url,

        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $description,

        #[Assert\NotNull]
        public readonly string $credit,
    ) {
    }
}
