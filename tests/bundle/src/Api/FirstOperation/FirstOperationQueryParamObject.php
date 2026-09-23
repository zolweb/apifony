<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

use Symfony\Component\Validator\Constraints as Assert;
class FirstOperationQueryParamObject
{
    /**
     * @param string $stringProperty
     * @param list<int<min,max>> $nestedArrayProperty
     * @param FirstOperationQueryParamObjectNestedObjectProperty $nestedObjectProperty
     * @param string $optionalProperty
     */
    public function __construct(
        
        public readonly string $stringProperty,
        
        #[Assert\All(constraints: [new Assert\Type(type: 'int'), new Assert\NotNull()])]
        public readonly array $nestedArrayProperty,
        
        #[Assert\Valid]
        public readonly FirstOperationQueryParamObjectNestedObjectProperty $nestedObjectProperty,
        
        public readonly string $optionalProperty = 'abc'
    )
    {
    }
}