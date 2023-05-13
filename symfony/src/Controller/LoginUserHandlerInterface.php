<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    /**
     * OperationId: loginUser
     */
    public function handle(
        ?string $password
        ?string $username
    ): ;
}
