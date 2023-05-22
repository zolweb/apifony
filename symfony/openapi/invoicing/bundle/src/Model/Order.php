<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class Order
{
    public function __construct(
        public readonly int $id,

        public readonly int $petId,

        #[Assert\GreaterThan(value: 5)]
        public readonly int $quantity,

        #[Assert\NotNull]
        #[AssertFormat\DateTime]
        public readonly string $shipDate,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
            'placed',
            'approved',
            'delivered',
        ])]
        public readonly string $status,

        public readonly bool $complete,
    ) {
    }
}
