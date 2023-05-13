<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $petId,
        public readonly ?int $quantity,
        #[date-time]
        public readonly ?string $shipDate,
        #[Assert\Choice(choices: [
                0,
1,
2,
                ])]
        public readonly ?string $status,
        public readonly ?bool $complete,
    ) {
    }
}
