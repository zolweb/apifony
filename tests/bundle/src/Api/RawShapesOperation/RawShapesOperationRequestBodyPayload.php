<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation;

class RawShapesOperationRequestBodyPayload
{
    /**
     * @param mixed $nullableRaw
     * @param mixed $refRaw
     */
    public function __construct(
        
        public readonly mixed $nullableRaw,
        
        public readonly mixed $refRaw
    )
    {
    }
}