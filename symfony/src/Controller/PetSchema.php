<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetSchema
{
    /**
     * @param @param array<string> $photoUrls
     * @param @param ?array<TagsPetSchema> $tags
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id,
        #[Assert\NotNull()]
        public readonly string $name,
        public readonly ?CategoryPetSchema $category,
        #[Assert\NotNull()]
        public readonly array $photoUrls,
        public readonly ?array $tags,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status,
    ) {
    }
}