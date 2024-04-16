<?php

namespace Zol\TestOpenApiServer\Api\Locale;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Model\Locale;


class GetLocaleList200ApplicationJsonResponsePayload
{
    /**
     * @param array<Locale> $data
     */
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $data,

        #[Assert\NotNull]
        public readonly string $default,
    ) {
    }
}
