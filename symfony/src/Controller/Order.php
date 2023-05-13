<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    public function __construct(
        public readonly ?int $id,

        public readonly ?int $petId,

        #[Assert\GreaterThan(value: 5)]
        public readonly ?int $quantity,

        #[date-time]
        public readonly ?string $shipDate = '',

        #[Assert\Choice(choices: [
            'placed',
            'approved',
            'delivered',
        ])]
        public readonly ?string $status = '',

        public readonly ?bool $complete,
    ) {
    }
}
