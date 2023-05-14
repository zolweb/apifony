<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Pet
{
    /**
     * @param array<string> $photoUrls
     * @param ?array<Tag> $tags
     */
    public function __construct(
        public readonly ?int $id,

        #[Assert\NotNull]
        #[Assert\Length(min: 2)]
        #[Assert\Length(max: 5)]
        #[Assert\Choice(choices: [
            'cool',
            'flex',
            'lol',
        ])]
        public readonly string $name = 'cool',

        #[Assert\Valid]
        public readonly ?Category $category,

        #[Assert\NotNull]
        #[Assert\All(constraints: [
            new Assert\Length(min: 3),
            new Assert\Length(max: 10),
        ])]
        public readonly array $photoUrls,

        #[Assert\Count(min: 2)]
        #[Assert\Count(max: 5)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\Valid,
        ])]
        public readonly ?array $tags,

        #[DateTime]
        #[Assert\Regex(pattern: 'A-z')]
        #[Assert\Choice(choices: [
            'available',
            'pending',
            'sold',
        ])]
        public readonly ?string $status = '',

        #[Assert\Valid]
        public readonly ?PetOwner $owner,
    ) {
    }
}
