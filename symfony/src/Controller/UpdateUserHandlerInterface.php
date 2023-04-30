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
        string $username = null,
        UserSchema $dto,
    ): UpdateUser201EmptyResponse;
}
