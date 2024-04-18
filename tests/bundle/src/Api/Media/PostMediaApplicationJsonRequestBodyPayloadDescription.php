<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

use Symfony\Component\Validator\Constraints as Assert;
class PostMediaApplicationJsonRequestBodyPayloadDescription
{
    public function __construct(#[Assert\NotNull] public readonly string $localeId, #[Assert\NotNull] public readonly string $content)
    {
    }
}