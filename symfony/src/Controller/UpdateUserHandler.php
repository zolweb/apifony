<?php

namespace App\Controller;

interface UpdateUserHandler
{
    public function handle(
        string $username,
    );
}
