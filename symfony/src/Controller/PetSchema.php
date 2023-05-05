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
        #[Assert\Valid()]
        public readonly ?CategorySchema $category,
        #[Assert\NotNull()]
        #[Assert\All([
			new Assert\NotNull(),
			new Assert\Length(min: 3),
			new Assert\Length(max: 10),
		])]
        public readonly array $photoUrls,
        #[Assert\Count(min: 2)]
        #[Assert\Count(max: 5)]
        #[Assert\Unique()]
        #[Assert\All([
			new Assert\Valid(),
		])]
        public readonly ?array $tags,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status,
    ) {
    }
}