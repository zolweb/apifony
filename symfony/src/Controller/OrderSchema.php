<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class OrderSchema
{
    /**
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id = null,
        #[Int64()]
        public readonly ?int $petId = null,
        #[Int32()]
        public readonly ?int $quantity = null,
        #[DateTime()]
        public readonly ?string $shipDate = null,
        #[Assert\Choice(['placed', 'approved', 'delivered'])]
        public readonly ?string $status = null,
        public readonly ?bool $complete = null,
    ) {
    }
}