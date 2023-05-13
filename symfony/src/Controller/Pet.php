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
        #[Assert\Regex(pattern: 'A-z')]
        #[Assert\Length(min: 2)]
        #[Assert\Length(max: 5)]
        public readonly ?string $name,
        #[Assert\Valid]
        public readonly ?Category $category,
        #[Assert\All(constraints: [
                0,
1,
                ])]
        public readonly ?array $photoUrls,
        #[Assert\Count(min: 2)]
        #[Assert\Count(max: 5)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
                0,
                ])]
        public readonly ?array $tags,
        #[Assert\Choice(choices: [
                0,
1,
2,
                ])]
        public readonly ?string $status,
        #[Assert\Valid]
        public readonly ?PetOwner $owner,
        #[Assert\Count(min: 1)]
        #[Assert\Count(max: 6)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
                0,
1,
2,
                ])]
        public readonly ?array $parentArray,
    ) {
    }
}
