<?php

namespace App\Controller;

class OrderSchema
{
    /**
    */
    public function __construct(
        public readonly int $id,
        public readonly int $petId,
        public readonly int $quantity,
        public readonly string $shipDate,
        public readonly string $status,
        public readonly bool $complete,
    ) {
    }
}