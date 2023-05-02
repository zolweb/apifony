<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetSchema
{
    /**
     * @param @param array<array> $photoUrls
     * @param @param ?array<array> $tags
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id = null,
        #[Assert\NotNull()]
        public readonly string $name = null,
        public readonly ?mixed $category = null,
        #[Assert\NotNull()]
        public readonly array $photoUrls = null,
        public readonly ?array $tags = null,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status = null,
    ) {
    }
}