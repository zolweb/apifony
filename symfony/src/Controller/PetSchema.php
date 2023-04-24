<?php

namespace App\Controller;

class PetSchema
{
    /**
     * @param array<string> $photoUrls
     * @param array<TagSchema> $tags,
    */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly CategorySchema $category,
        public readonly array $photoUrls,
        public readonly array $tags,
        public readonly string $status,
    ) {
    }
}