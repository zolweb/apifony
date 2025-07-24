<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
class SchemaObjectProperty
{
    public function __construct(
        
        #[Assert\NotNull]
        public readonly string $stringProperty
    )
    {
    }
}