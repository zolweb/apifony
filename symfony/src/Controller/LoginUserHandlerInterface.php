<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    /**
     * OperationId: loginUser
     */
    public function handle(
        ?string $qPassword = ''
        ?string $qUsername = ''
    ): ;
}
