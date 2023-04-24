<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetSchema
{
    /**
     * @param array<string> $photoUrls
     * @param ?array<TagSchema> $tags,
    */
    public function __construct(
        public readonly ?int $id = null,
        #[Assert\NotNull]
        public readonly string $name,
        #[Assert\Valid]
        public readonly ?CategorySchema $category = null,
        #[Assert\NotNull]
        public readonly array $photoUrls,
        #[Assert\Valid]
        public readonly ?array $tags = null,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status = null,
    ) {
    }
}