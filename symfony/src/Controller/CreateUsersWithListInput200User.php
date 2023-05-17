<?php

namespace App\Controller;

class CreateUsersWithListInput200User
{
    public const code = '200';

    public function __construct(
        public readonly User $content,
    ) {
    }
}