<?php

namespace App\Controller;

interface UpdateUserHandlerInterface
{
    public function handle(
        string $pUsername = ''
    ): ;
}
