<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Email as AssertEmail;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Uuid as AssertUuid;
use Zol\Ogen\Tests\TestOpenApiServer\Format\DateTime as AssertDateTime;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Date as AssertDate;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Time as AssertTime;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Custom as AssertCustom;
class Schema
{
    /**
     * @param list<string> $arrayProperty
     * @param list<SchemaObjectArrayProperty> $objectArrayProperty
     * @param list<Schema> $recursiveObjectArray
     */
    public function __construct(
        
        #[Assert\NotNull] public readonly string $stringProperty,
        
        #[Assert\NotNull] public readonly float $numberProperty,
        
        #[Assert\NotNull] public readonly int $integerProperty,
        
        #[Assert\NotNull] public readonly bool $booleanProperty,
        
        #[Assert\NotNull] #[AssertEmail] public readonly string $emailProperty,
        
        #[Assert\NotNull] #[AssertUuid] public readonly string $uuidProperty,
        
        #[Assert\NotNull] #[AssertDateTime] public readonly string $dateTimeProperty,
        
        #[Assert\NotNull] #[AssertDate] public readonly string $dateProperty,
        
        #[Assert\NotNull] #[AssertTime] public readonly string $timeProperty,
        
        #[Assert\NotNull] #[AssertCustom] public readonly string $customProperty,
        
        #[Assert\Valid] #[Assert\NotNull] public readonly SchemaObjectProperty $objectProperty,
        
        #[Assert\NotNull] #[Assert\All(constraints: array(new Assert\NotNull()))] public readonly array $arrayProperty,
        
        #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: array(new Assert\NotNull()))] public readonly array $objectArrayProperty,
        
        #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: array(new Assert\NotNull()))] public readonly array $recursiveObjectArray
    )
    {
    }
}