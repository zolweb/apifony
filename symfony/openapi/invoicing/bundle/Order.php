<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    public function __construct(
        #[Assert\NotNull]
        #[Int64]
        public readonly int $id,

        #[Assert\NotNull]
        #[Int64]
        public readonly int $petId,

        #[Assert\GreaterThan(value: 5)]
        #[Assert\NotNull]
        #[Int32]
        public readonly int $quantity,

        #[Assert\NotNull]
        #[DateTime]
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
