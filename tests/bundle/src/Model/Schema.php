<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Custom as AssertCustom;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Email as AssertEmail;

class Schema
{
    /**
     * @param list<string>                    $arrayProperty
     * @param list<SchemaObjectArrayProperty> $objectArrayProperty
     * @param list<Schema>                    $recursiveObjectArray
     */
    public function __construct(
        #[Assert\NotNull] public readonly string $stringProperty,
        #[Assert\NotNull] public readonly float $numberProperty,
        #[Assert\NotNull] public readonly int $integerProperty,
        #[Assert\NotNull] public readonly bool $booleanProperty,
        #[Assert\NotNull] #[AssertEmail] public readonly string $emailProperty,
        #[Assert\NotNull] #[AssertCustom] public readonly string $customProperty,
        #[Assert\Valid] #[Assert\NotNull] public readonly SchemaObjectProperty $objectProperty,
        #[Assert\NotNull] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $arrayProperty,
        #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $objectArrayProperty,
        #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $recursiveObjectArray
    ) {
    }
}
