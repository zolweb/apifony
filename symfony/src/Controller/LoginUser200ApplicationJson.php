<?php

namespace App\Controller;

class LoginUser200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly string $content,
        public readonly int $X-Rate-Limit,
        public readonly string $X-Expires-After,
    ) {
    }
}