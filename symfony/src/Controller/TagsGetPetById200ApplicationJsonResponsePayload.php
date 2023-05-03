<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class TagsGetPetById200ApplicationJsonResponsePayload
{
    /**
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id,
        public readonly ?string $name,
    ) {
    }
}