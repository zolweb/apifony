<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    public function __construct(
        #[Int64]
        public readonly ?int $id,

        #[Int64]
        public readonly ?int $petId,

        #[Assert\GreaterThan(value: 5)]
        #[Int32]
        public readonly ?int $quantity,

        #[DateTime]
        public readonly ?string $shipDate,

        #[Assert\Choice(choices: [
            'placed',
            'approved',
            'delivered',
        ])]
        public readonly ?string $status,

        public readonly ?bool $complete,
    ) {
    }
%}
