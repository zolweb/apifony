<?php

namespace App\Controller;

interface CreateUserHandlerInterface
{
    public function handleEmptyApplicationJson(
    ):
        CreateUser201User;

    public function handleUserApplicationJson(
        User $content,
    ):
        CreateUser201User;
}
