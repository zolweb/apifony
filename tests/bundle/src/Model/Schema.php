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
     * @param list<string> $arrayProperty
     * @param list<SchemaObjectArrayProperty> $objectArrayProperty
     * @param list<Schema> $recursiveObjectArray
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