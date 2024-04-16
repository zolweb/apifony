<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Symfony\Component\Validator\Constraints as Assert;


class PublishContentTranslationApplicationJsonRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $version,
    ) {
    }
}
