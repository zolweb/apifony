<?php

namespace App\Controller;

interface GetUserByNameHandler
{
    public function handle(
        string $username,
    ): void;
}
