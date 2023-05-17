<?php

namespace App\Controller;

class PostTest200Test
{
    public const code = '200';

    public function __construct(
        public readonly Test $content,
        public readonly string $h1,
        public readonly int $h2,
        public readonly float $h3,
        public readonly bool $h4,
    ) {
    }
}