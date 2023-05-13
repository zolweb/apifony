<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    /**
     * OperationId: loginUser
     */
    public function handle(
        ?mixed $qPassword,
        ?mixed $qUsername,
    ): ;
}
