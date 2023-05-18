<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class Pet
{
    /**
     * @param array<string> $photoUrls
     * @param array<Tag> $tags
     */
    public function __construct(
        #[Assert\NotNull]
        #[Int64]
        public readonly int $id,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly Category $category,

        #[Assert\All(constraints: [
            new Assert\Length(min: 3),
            new Assert\Length(max: 10),
            new Assert\NotNull,
        ])]
        #[Assert\NotNull]
        public readonly array $photoUrls,

        #[Assert\Count(min: 2)]
        #[Assert\Count(max: 5)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\Valid,
            new Assert\NotNull,
        ])]
        #[Assert\NotNull]
        public readonly array $tags,

        #[Assert\Regex(pattern: 'A-z')]
        #[Assert\Choice(choices: [
            'available',
            'pending',
            'sold',
        ])]
        #[Assert\NotNull]
        #[DateTime]
        public readonly string $status,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwner $owner,

        #[Assert\Length(min: 2)]
        #[Assert\Length(max: 5)]
        #[Assert\Choice(choices: [
            'cool',
            'flex',
            'lol',
        ])]
        #[Assert\NotNull]
        public readonly string $name = 'cool',
    ) {
    }
}
