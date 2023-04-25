<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PlaceOrder200ApplicationJsonResponsePayload
{
    /**
    */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $petId = null,
        public readonly ?int $quantity = null,
        public readonly ?string $shipDate = null,
        #[Assert\Choice(['placed', 'approved', 'delivered'])]
        public readonly ?string $status = null,
        public readonly ?bool $complete = null,
    ) {
    }
}