<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F25 as AssertF25;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F21 as AssertF21;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F22 as AssertF22;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F23 as AssertF23;

class Test
{
    /**
     * @param array<string> $a6
     * @param array<Test> $a7
     */
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Count(min: 1)]
        #[Assert\Count(max: 3)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\NotNull,
            new AssertF25,
            new Assert\Regex(pattern: '/[a-z]{3}/'),
            new Assert\Length(min: 3),
            new Assert\Length(max: 3),
            new Assert\Choice(choices: [
                'abc',
                'def',
                'ghi',
            ]),
        ])]
        public readonly array $a6,

        #[Assert\NotNull]
        #[Assert\Count(min: 1)]
        #[Assert\Count(max: 3)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\Valid,
            new Assert\NotNull,
        ])]
        public readonly array $a7,

        #[Assert\NotNull]
        #[AssertF21]
        #[Assert\Regex(pattern: '/[a-z]{3}/')]
        #[Assert\Length(min: 3)]
        #[Assert\Length(max: 3)]
        #[Assert\Choice(choices: [
            'abc',
            'def',
            'ghi',
        ])]
        public readonly string $a1 = 'abc',

        #[Assert\NotNull]
        #[AssertF22]
        #[Assert\DivisibleBy(value: 1)]
        #[Assert\GreaterThanOrEqual(value: 1)]
        #[Assert\LessThanOrEqual(value: 3)]
        #[Assert\Choice(choices: [
            1,
            2,
            3,
        ])]
        public readonly int $a2 = 1,

        #[Assert\NotNull]
        #[AssertF23]
        #[Assert\DivisibleBy(value: 0.1)]
        #[Assert\GreaterThanOrEqual(value: 0)]
        #[Assert\LessThanOrEqual(value: 1)]
        #[Assert\Choice(choices: [
            0.1,
            0.2,
            0.3,
        ])]
        public readonly float $a3 = 0.1,

        #[Assert\NotNull]
        public readonly bool $a4 = true,
    ) {
    }
}
