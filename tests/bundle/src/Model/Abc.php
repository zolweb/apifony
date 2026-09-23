<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Model;

class Abc
{
    /**
     * @param string $def
     */
    public function __construct(
        
        public readonly string $def
    )
    {
    }
}