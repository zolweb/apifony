<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation;

class RawShapesOperation200ResponsePayload
{
    /**
     * @param mixed $typelessEcho
     * @param mixed $stringEcho
     * @param mixed $nullableRawEcho
     * @param mixed $refRawEcho
     */
    public function __construct(
        
        public readonly mixed $typelessEcho,
        
        public readonly mixed $stringEcho,
        
        public readonly mixed $nullableRawEcho,
        
        public readonly mixed $refRawEcho
    )
    {
    }
}