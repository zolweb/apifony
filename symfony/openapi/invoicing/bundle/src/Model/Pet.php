<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64 as AssertInt64;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\DateTime as AssertDateTime;

class Pet
{
    /**
     * @param array<string> $photoUrls
     * @param array<Tag> $tags
     */
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        public readonly int $id,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly Category $category,

        #[Assert\NotNull]
        #[Assert\All(constraints: [
            new Assert\NotNull,
            new Assert\Length(min: 3),
            new Assert\Length(max: 10),
        ])]
        public readonly array $photoUrls,

        #[Assert\NotNull]
        #[Assert\Count(min: 2)]
        #[Assert\Count(max: 5)]
        #[Assert\Unique]
        #[Assert\All(constraints: [
            new Assert\Valid,
            new Assert\NotNull,
        ])]
        public readonly array $tags,

        #[Assert\NotNull]
        #[AssertDateTime]
        #[Assert\Regex(pattern: 'A-z')]
        #[Assert\Choice(choices: [
            'available',
            'pending',
            'sold',
        ])]
        public readonly string $status,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwner $owner,

        #[Assert\NotNull]
        #[Assert\Length(min: 2)]
        #[Assert\Length(max: 5)]
        #[Assert\Choice(choices: [
            'cool',
            'flex',
            'lol',
        ])]
        public readonly string $name = 'cool',
    ) {
    }
}
