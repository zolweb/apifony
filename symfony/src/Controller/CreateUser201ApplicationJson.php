<?php

namespace App\Controller;

class CreateUser201ApplicationJson{
    public const code = '201';

    public function __construct(
        public readonly User $content,
    ) {
    }
}