<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class Test
{
    /**
     * @param array<string> $a6
     * @param array<Test> $a7
     */
    public function __construct(
        #[Assert\Valid]
        public readonly Test $a5,

        #[Assert\Count(min: 1)]
        #[Assert\Count(max: 3)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\NotNull,
            new AssertFormat\F25,
            new Assert\Regex(pattern: '[a-z]{3}'),
            new Assert\Length(min: 3),
            new Assert\Length(max: 3),
            new Assert\Choice(choices: [
                'abc',
                'def',
                'ghi',
            ]),
        ])]
        public readonly array $a6,

        #[Assert\Count(min: 0)]
        #[Assert\Count(max: 3)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\Valid,
        ])]
        public readonly array $a7,

        #[Assert\NotNull]
        #[AssertFormat\F21]
        #[Assert\Regex(pattern: '[a-z]{3}')]
        #[Assert\Length(min: 3)]
        #[Assert\Length(max: 3)]
        #[Assert\Choice(choices: [
            'abc',
            'def',
            'ghi',
        ])]
        public readonly string $a1 = 'abc',

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\GreaterThanOrEqual(value: 1)]
        #[Assert\LessThanOrEqual(value: 3)]
        #[Assert\Choice(choices: [
            1,
            2,
            3,
        ])]
        public readonly int $a2 = 1,

        #[Assert\DivisibleBy(value: 0.1)]
        #[Assert\GreaterThanOrEqual(value: 0)]
        #[Assert\LessThanOrEqual(value: 1)]
        #[Assert\Choice(choices: [
            0.1,
            0.2,
            0.3,
        ])]
        public readonly float $a3 = 0.1,

        public readonly bool $a4 = true,
    ) {
    }
}
