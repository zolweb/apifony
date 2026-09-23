<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation;

class RawOperation200ResponsePayload
{
    /**
     * @param mixed $bodyEcho
     * @param mixed $paramEcho
     */
    public function __construct(
        
        public readonly mixed $bodyEcho,
        
        public readonly mixed $paramEcho
    )
    {
    }
}