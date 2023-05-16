<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetInventoryApplicationJsonMediaTypeSchema
{
    public function __construct(
        public readonly ?string $name,
    ) {
    }
}
