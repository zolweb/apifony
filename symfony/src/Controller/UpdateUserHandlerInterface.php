<?php

namespace App\Controller;

interface UpdateUserHandlerInterface
{
    /**
     * OperationId: updateUser
     */
    public function handle(
        string $username
    ): ;
}
