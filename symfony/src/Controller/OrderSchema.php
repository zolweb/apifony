<?php

namespace App\Controller;

class OrderSchema
{
    public function __construct(
        int $id,
        int $petId,
        int $quantity,
        string $shipDate,
        string $status,
        bool $complete,
    ) {
    }
}