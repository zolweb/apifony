<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\MediaTree;

use Symfony\Component\Validator\Constraints as Assert;
class UpdateMediaFolder400ApplicationJsonResponsePayload
{
    public function __construct(#[Assert\NotNull] public readonly string $code, #[Assert\NotNull] public readonly string $description)
    {
    }
}