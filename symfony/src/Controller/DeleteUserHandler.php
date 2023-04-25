<?php

namespace App\Controller;

interface DeleteUserHandler
{
    public function handle(
        string $username,
    ): void
}}
