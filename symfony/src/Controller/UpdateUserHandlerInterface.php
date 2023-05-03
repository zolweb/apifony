<?php

namespace App\Controller;

interface UpdateUserHandlerInterface
{
    /**
     * OperationId: updateUser
     *
     * Update user
     *
     * This can only be done by the logged in user.
     */
    public function handle(
        string $pUsername,
        UserSchema $payload,
    ): UpdateUser201EmptyResponse;
}