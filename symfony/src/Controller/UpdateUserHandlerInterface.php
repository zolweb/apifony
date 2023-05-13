<?php

namespace App\Controller;

interface UpdateUserHandlerInterface
{
    /**
     * OperationId: updateUser
     */
    public function handle(
        ?mixed $pUsername,
    ): ;
}
