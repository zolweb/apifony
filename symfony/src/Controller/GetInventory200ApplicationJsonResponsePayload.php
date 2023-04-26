<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetInventory200ApplicationJsonResponsePayload
{
    /**
    */
    public function __construct(
        public readonly ?string $name = null,
    ) {
    }
}