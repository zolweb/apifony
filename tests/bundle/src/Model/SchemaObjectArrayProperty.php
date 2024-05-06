<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
class SchemaObjectArrayProperty
{
    public function __construct(
        
        #[Assert\NotNull] public readonly string $stringProperty
    )
    {
    }
}