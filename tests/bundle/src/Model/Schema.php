<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
class Schema
{
    /**
     * @param array<string> $arrayProperty
     * @param array<SchemaObjectArrayProperty> $objectArrayProperty
     * @param array<Schema> $recursiveObjectArray
     */
    public function __construct(#[Assert\NotNull] public readonly string $stringProperty, #[Assert\NotNull] public readonly float $numberProperty, #[Assert\NotNull] public readonly int $integerProperty, #[Assert\NotNull] public readonly bool $booleanProperty, #[Assert\Valid] #[Assert\NotNull] public readonly SchemaObjectProperty $objectProperty, #[Assert\NotNull] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $arrayProperty, #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $objectArrayProperty, #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $recursiveObjectArray)
    {
    }
}