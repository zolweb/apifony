<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Pet
{
    /**
     * @param ?array<string> $photoUrls
     * @param ?array<PetTags> $tags
     */
    public function __construct(
        #[Lol()]
        public readonly ?int $id,
        public readonly ?string $name,
        #[Assert\Valid()]
        public readonly ?Category $category,
        public readonly ?array $photoUrls,
        #[Assert\Unique()]
        public readonly ?array $tags,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status,
        #[Assert\Valid()]
        public readonly ?PetOwner $owner,
    ) {
    }
}
