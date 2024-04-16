<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;


class PatchMediaApplicationJsonRequestBodyPayload
{
    /**
     * @param array<PatchMediaApplicationJsonRequestBodyPayloadDescription> $description
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,

        #[Assert\NotNull]
        public readonly string $name,

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
