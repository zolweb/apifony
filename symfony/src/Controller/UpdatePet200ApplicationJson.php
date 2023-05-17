<?php

namespace App\Controller;

class UpdatePet200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly Pet $content,
    ) {
    }
}