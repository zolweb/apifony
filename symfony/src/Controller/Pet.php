<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Pet
{
    /**
     * @param array<string> $photoUrls
     * @param array<Tag> $tags
     */
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?Category $category,
        public readonly ?array $photoUrls,
        public readonly ?array $tags,
        public readonly ?string $status,
        public readonly ?PetOwner $owner,
    ) {
    }
}
