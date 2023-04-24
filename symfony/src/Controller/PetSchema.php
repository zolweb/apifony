<?php

namespace App\Controller;

class PetSchema
{
    public function __construct(
        int $id,
        string $name,
        CategorySchema $category,
        array $photoUrls,
        array $tags,
        string $status,
    ) {
    }
}