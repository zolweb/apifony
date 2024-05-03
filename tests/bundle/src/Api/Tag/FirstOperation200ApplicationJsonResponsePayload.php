<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Tag;

use Symfony\Component\Validator\Constraints as Assert;
class FirstOperation200ApplicationJsonResponsePayload
{
    public function __construct(#[Assert\NotNull] public readonly string $dump)
    {
    }
}