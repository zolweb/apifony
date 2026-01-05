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
     * @param string $dateProperty
     * @param string $timeProperty
     * @param string $customProperty
     * @param ?string $nullDefaultProperty
     * @param SchemaObjectProperty $objectProperty
     * @param list<string> $arrayProperty
     * @param list<SchemaObjectArrayProperty> $objectArrayProperty
     * @param list<Schema> $recursiveObjectArray
     * @param string $defaultProperty
     * @param string $overriddenProperty
     */
    public function __construct(
        
        #[Assert\NotNull]
        public readonly string $stringProperty,
        
        #[Assert\NotNull]
        public readonly float $numberProperty,
        
        #[Assert\NotNull]
        public readonly int $integerProperty,
        
        #[Assert\NotNull]
        public readonly bool $booleanProperty,
        
        #[Assert\NotNull]
        #[Assert\Choice(choices: ['abc', 'def', 'ghi'])]
        public readonly string $enumStringProperty,
        
        #[Assert\Choice(choices: ['abc', 'def', 'ghi', null])]
        public readonly ?string $enumNullableStringProperty,
        
        #[Assert\NotNull]
        #[Assert\GreaterThanOrEqual(value: -5)]
        #[Assert\LessThanOrEqual(value: 5)]
        public readonly int $integerRangeProperty,
        
        #[Assert\NotNull]
        #[AssertEmail]
        public readonly string $emailProperty,
        
        #[Assert\NotNull]
        #[AssertUuid]
        public readonly string $uuidProperty,
        
        #[Assert\NotNull]
        #[AssertDateTime]
        public readonly string $dateTimeProperty,
        
        #[Assert\NotNull]
        #[AssertDate]
        public readonly string $dateProperty,
        
        #[Assert\NotNull]
        #[AssertTime]
        public readonly string $timeProperty,
        
        #[Assert\NotNull]
        #[AssertCustom]
        public readonly string $customProperty,
        
        public readonly ?string $nullDefaultProperty,
        
        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly SchemaObjectProperty $objectProperty,
        
        #[Assert\NotNull]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $arrayProperty,
        
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $objectArrayProperty,
        
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $recursiveObjectArray,
        
        #[Assert\NotNull]
        public readonly string $defaultProperty = 'abc',
        
        #[Assert\NotNull]
        public readonly string $overriddenProperty = 'def'
    )
    {
    }
}