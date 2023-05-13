<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Pet
{
    /**
     * @param array<string> $photoUrls
     * @param array<Tag> $tags
     * @param array<array<PetParentArray>> $parentArray
     */
    public function __construct(
        public readonly ?int $id,
        #[Assert\Regex(
            pattern: 'A-z',
        )]
        #[Assert\Length(
            min: 2,
        )]
        #[Assert\Length(
            max: 5,
        )]
        public readonly ?string $name,
        #[Assert\Valid]
        public readonly ?Category $category,
        public readonly ?array $photoUrls,
        public readonly ?array $tags,
        public readonly ?string $status,
        #[Assert\Valid]
        public readonly ?PetOwner $owner,
        public readonly ?array $parentArray,
    ) {
    }
}
