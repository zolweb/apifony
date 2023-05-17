<?php

namespace App\Controller;

class AddPet200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly Pet $content,
    ) {
    }
}