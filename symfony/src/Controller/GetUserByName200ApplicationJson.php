<?php

namespace App\Controller;

class GetUserByName200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly User $content,
    ) {
    }
}