<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetSchema
{
    /**
     * @param array<string> $photoUrls
     * @param ?array<TagSchema> $tags
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id,
        #[Assert\NotNull()]
        public readonly string $name,
        public readonly ?CategorySchema $category,
        #[Assert\NotNull()]
        public readonly array $photoUrls,
        public readonly ?array $tags,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status,
    ) {
    }
}