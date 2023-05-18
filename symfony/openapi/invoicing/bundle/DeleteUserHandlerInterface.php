<?php

namespace App\Controller;

interface DeleteUserHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        DeleteUser200Empty |
        DeleteUser400Empty |
        DeleteUser404Empty;
}
