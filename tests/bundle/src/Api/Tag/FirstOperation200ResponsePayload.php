<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Tag;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Abc;
class FirstOperation200ResponsePayload
{
    /**
     * @param list<Abc> $abcList1
     * @param list<Abc> $abcList2
     */
    public function __construct(
        #[Assert\NotNull] public readonly string $dump,
        #[Assert\NotNull] public readonly string $string_string,
        #[Assert\Valid] #[Assert\NotNull] public readonly Abc $abc1,
        #[Assert\Valid] #[Assert\NotNull] public readonly Abc $abc2,
        #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $abcList1,
        #[Assert\NotNull] #[Assert\Valid] #[Assert\All(constraints: [new Assert\NotNull()])] public readonly array $abcList2
    ) {
    }
}