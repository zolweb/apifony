<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format as AssertFormat;

class Order
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertFormat\Int64]
        public readonly int $id,

        #[Assert\NotNull]
        #[AssertFormat\Int64]
        public readonly int $petId,

        #[Assert\GreaterThan(value: 5)]
        #[Assert\NotNull]
        #[AssertFormat\Int32]
        public readonly int $quantity,

        #[Assert\NotNull]
        #[AssertFormat\DateTime]
        public readonly string $shipDate,

        #[Assert\Choice(choices: [
            'placed',
            'approved',
            'delivered',
        ])]
        #[Assert\NotNull]
        public readonly string $status,

        #[Assert\NotNull]
        public readonly bool $complete,
    ) {
    }
}
