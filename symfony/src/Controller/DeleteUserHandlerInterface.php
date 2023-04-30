<?php

namespace App\Controller;

interface DeleteUserHandlerInterface
{
    /**
     * OperationId: deleteUser
     *
     * Delete user
     *
     * This can only be done by the logged in user.
     */
    public function handle(
        string $username = null,
    ): DeleteUser400EmptyResponse|DeleteUser404EmptyResponse;
}
