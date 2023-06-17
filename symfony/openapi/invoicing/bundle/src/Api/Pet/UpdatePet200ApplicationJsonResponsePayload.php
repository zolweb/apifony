<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePet200ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $title,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
