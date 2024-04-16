<?php

namespace Zol\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;


class MediaDescription
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $localeId,

        #[Assert\NotNull]
        public readonly string $content,
    ) {
    }
}
