<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

use Symfony\Component\Validator\Constraints as Assert;
class PostAtom400ApplicationJsonResponsePayload
{
    public function __construct(#[Assert\NotNull] public readonly string $code)
    {
    }
}