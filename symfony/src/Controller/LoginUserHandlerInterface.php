<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    public function handleNullApplicationJson(
            string $qPassword,
            string $qUsername,
    );
}
