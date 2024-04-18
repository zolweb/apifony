<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Symfony\Component\Validator\Constraints as Assert;
class PatchContentTranslationApplicationJsonRequestBodyPayload
{
    /**
     * @param array<mixed>|\stdClass $data
     */
    public function __construct(#[Assert\NotNull] public readonly int $version, #[Assert\NotNull] public readonly array|\stdClass $data)
    {
    }
}