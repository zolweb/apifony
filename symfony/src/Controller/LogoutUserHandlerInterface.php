<?php

namespace App\Controller;

interface LogoutUserHandlerInterface
{
    /**
     * OperationId: logoutUser
     *
     * Logs out current logged in user session
     */
    public function handle(
    ): LogoutUser100EmptyResponse;
}