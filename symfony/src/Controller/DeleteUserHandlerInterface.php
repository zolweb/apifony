<?php

namespace App\Controller;

interface DeleteUserHandlerInterface
{
    /**
     * OperationId: deleteUser
     */
    public function handle(
        string $username
    ): ;
}
