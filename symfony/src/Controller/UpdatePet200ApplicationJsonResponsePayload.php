<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePet200ApplicationJsonResponsePayload
{
    /**
     * @param @param array<string> $photoUrls
     * @param @param ?array<TagsUpdatePet200ApplicationJsonResponsePayload> $tags
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id,
        #[Assert\NotNull()]
        public readonly string $name,
        public readonly ?CategoryUpdatePet200ApplicationJsonResponsePayload $category,
        #[Assert\NotNull()]
        public readonly array $photoUrls,
        public readonly ?array $tags,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status,
    ) {
    }
}