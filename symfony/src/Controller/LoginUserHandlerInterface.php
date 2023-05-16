<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    public function handle(
        string $qPassword,
        string $qUsername,
    ) ;
}
