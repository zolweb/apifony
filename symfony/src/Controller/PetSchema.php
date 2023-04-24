<?php

namespace App\Controller;

class PetSchema
{
    /**
     * @param array<string> $photoUrls
     * @param ?array<TagSchema> $tags,
    */
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $name,
        public readonly ?CategorySchema $category = null,
        public readonly array $photoUrls,
        public readonly ?array $tags = null,
        public readonly ?string $status = null,
    ) {
    }
}