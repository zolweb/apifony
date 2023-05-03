<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class OrderSchema
{
    /**
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id,
        #[Int64()]
        public readonly ?int $petId,
        #[Int32()]
        public readonly ?int $quantity,
        #[DateTime()]
        public readonly ?string $shipDate,
        #[Assert\Choice(['placed', 'approved', 'delivered'])]
        public readonly ?string $status,
        public readonly ?bool $complete,
    ) {
    }
}