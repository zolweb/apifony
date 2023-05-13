<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class 645f3da861eb9
{
    /**
    */
    public function __construct(
        #[Lol()]
        public readonly ?int $id,
        #[Lol()]
        public readonly ?int $petId,
        #[Lol()]
        public readonly ?int $quantity,
        #[Lol()]
        public readonly ?string $shipDate,
        #[Assert\Choice(['placed', 'approved', 'delivered'])]
        public readonly ?string $status,
        public readonly ?bool $complete,
    ) {
    }
}
