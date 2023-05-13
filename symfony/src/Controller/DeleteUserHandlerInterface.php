<?php

namespace App\Controller;

interface DeleteUserHandlerInterface
{
    public function handle(
        string $pUsername = ''
    ): ;
}
