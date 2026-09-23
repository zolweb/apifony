<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Model;

class SchemaObjectProperty
{
    /**
     * @param string $stringProperty
     */
    public function __construct(
        
        public readonly string $stringProperty
    )
    {
    }
}