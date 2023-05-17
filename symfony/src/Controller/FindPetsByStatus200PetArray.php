<?php

namespace App\Controller;

class FindPetsByStatus200PetArray
{
    public const code = '200';

    public function __construct(
        public readonly array $content,
    ) {
    }
}