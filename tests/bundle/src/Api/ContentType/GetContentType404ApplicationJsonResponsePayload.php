<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentType;

use Symfony\Component\Validator\Constraints as Assert;
class GetContentType404ApplicationJsonResponsePayload
{
    public function __construct(#[Assert\NotNull] public readonly string $code, #[Assert\NotNull] public readonly string $description)
    {
    }
}