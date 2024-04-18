<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
class ContentTranslationDataVersion
{
    /**
     * @param array<mixed>|\stdClass $data
     */
    public function __construct(#[Assert\NotNull] public readonly int $timestamp, #[Assert\NotNull] public readonly array|\stdClass $data)
    {
    }
}