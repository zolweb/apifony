<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
class ContentType
{
    /**
     * @param array<mixed>|\stdClass $schema
     */
    public function __construct(#[Assert\NotNull] public readonly string $id, #[Assert\NotNull] public readonly array|\stdClass $schema)
    {
    }
}