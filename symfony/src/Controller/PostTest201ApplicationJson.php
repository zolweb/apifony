<?php

namespace App\Controller;

class PostTest201ApplicationJson{
    public const code = '201';

    public function __construct(
        public readonly Test $content,
    ) {
    }
}