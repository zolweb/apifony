<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Format\Email as AssertEmail;
use Zol\Apifony\Tests\TestOpenApiServer\Format\Uuid as AssertUuid;
use Zol\Apifony\Tests\TestOpenApiServer\Format\DateTime as AssertDateTime;
use Zol\Apifony\Tests\TestOpenApiServer\Format\Date as AssertDate;
use Zol\Apifony\Tests\TestOpenApiServer\Format\Time as AssertTime;
use Zol\Apifony\Tests\TestOpenApiServer\Format\Custom as AssertCustom;
class Schema
{
    /**
     * @param string $stringProperty
     * @param float $numberProperty
     * @param int<min,max> $integerProperty
     * @param bool $booleanProperty
     * @param 'abc'|'def'|'ghi' $enumStringProperty
     * @param 'abc'|'def'|'ghi'|null $enumNullableStringProperty
     * @param int<-5,5> $integerRangeProperty
     * @param string $emailProperty
     * @param string $uuidProperty
     * @param string $dateTimeProperty
     * @param string $dateTimeProperty2
     * @param string $dateTimeProperty3
     * @param string $dateTimeProperty4
     * @param string $dateProperty
     * @param string $timeProperty
     * @param string $timeProperty2
     * @param string $timeProperty3
     * @param string $timeProperty4
     * @param string $customProperty
     * @param SchemaObjectProperty $objectProperty
     * @param list<string> $arrayProperty
     * @param mixed $rawProperty
     * @param list<list<int<min,max>>> $integerMatrixProperty
     * @param list<SchemaObjectArrayProperty> $objectArrayProperty
     * @param list<Schema> $recursiveObjectArray
     * @param string $defaultProperty
     * @param ?string $nullDefaultProperty
     * @param list<string> $emptyArrayDefaultProperty
     * @param string $overriddenProperty
     */
    public function __construct(
        
        public readonly string $stringProperty,
        
        public readonly float $numberProperty,
        
        public readonly int $integerProperty,
        
        public readonly bool $booleanProperty,
        
        #[Assert\Choice(choices: ['abc', 'def', 'ghi'])]
        public readonly string $enumStringProperty,
        
        #[Assert\Choice(choices: ['abc', 'def', 'ghi', null])]
        public readonly ?string $enumNullableStringProperty,
        
        #[Assert\GreaterThanOrEqual(value: -5)]
        #[Assert\LessThanOrEqual(value: 5)]
        public readonly int $integerRangeProperty,
        
        #[AssertEmail]
        public readonly string $emailProperty,
        
        #[AssertUuid]
        public readonly string $uuidProperty,
        
        #[AssertDateTime]
        public readonly string $dateTimeProperty,
        
        #[AssertDateTime]
        public readonly string $dateTimeProperty2,
        
        #[AssertDateTime]
        public readonly string $dateTimeProperty3,
        
        #[AssertDateTime]
        public readonly string $dateTimeProperty4,
        
        #[AssertDate]
        public readonly string $dateProperty,
        
        #[AssertTime]
        public readonly string $timeProperty,
        
        #[AssertTime]
        public readonly string $timeProperty2,
        
        #[AssertTime]
        public readonly string $timeProperty3,
        
        #[AssertTime]
        public readonly string $timeProperty4,
        
        #[AssertCustom]
        public readonly string $customProperty,
        
        #[Assert\Valid]
        public readonly SchemaObjectProperty $objectProperty,
        
        #[Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull()])]
        public readonly array $arrayProperty,
        
        public readonly mixed $rawProperty,
        
        #[Assert\All(constraints: [new Assert\Type(type: 'array'), new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: 'int'), new Assert\NotNull()])])]
        public readonly array $integerMatrixProperty,
        
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $objectArrayProperty,
        
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $recursiveObjectArray,
        
        public readonly string $defaultProperty = 'abc',
        
        public readonly ?string $nullDefaultProperty = null,
        
        #[Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull()])]
        public readonly array $emptyArrayDefaultProperty = [],
        
        public readonly string $overriddenProperty = 'def'
    )
    {
    }
}