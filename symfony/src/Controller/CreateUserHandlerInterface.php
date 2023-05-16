<?php

namespace App\Controller;

interface CreateUserHandlerInterface
{
    public function handleNullApplicationJson(
    );
    public function handleUserApplicationJson(
            User $content,
    );
}
