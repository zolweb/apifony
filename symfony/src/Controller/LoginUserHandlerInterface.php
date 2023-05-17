<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    public function handleEmptyApplicationJson(
        string $qPassword,
        string $qUsername,
    ):
        LoginUser200String;
}
