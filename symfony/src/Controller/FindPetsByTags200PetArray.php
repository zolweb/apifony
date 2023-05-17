<?php

namespace App\Controller;

class FindPetsByTags200PetArray
{
    public const code = '200';

    public function __construct(
        public readonly array $content,
    ) {
    }
}