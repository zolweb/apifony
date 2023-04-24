<?php

namespace App\Controller;

class OrderSchema
{
    /**
    */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $petId = null,
        public readonly ?int $quantity = null,
        public readonly ?string $shipDate = null,
        public readonly ?string $status = null,
        public readonly ?bool $complete = null,
    ) {
    }
}