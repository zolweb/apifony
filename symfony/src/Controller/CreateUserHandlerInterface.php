<?php

namespace App\Controller;

interface CreateUserHandlerInterface
{
    /**
     * OperationId: createUser
     *
     * Create user
     *
     * This can only be done by the logged in user.
     */
    public function handle(
        UserSchema $dto,
    ): CreateUser201ApplicationJsonResponse;
}
