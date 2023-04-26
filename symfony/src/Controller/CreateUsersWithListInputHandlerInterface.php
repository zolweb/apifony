<?php

namespace App\Controller;

interface CreateUsersWithListInputHandlerInterface
{
    /**
     * OperationId: createUsersWithListInput
     *
     * Creates list of users with given input array
     *
     * Creates list of users with given input array
     */
    public function handle(
        CreateUsersWithListInputRequestPayload $dto,
    ): CreateUsersWithListInput100EmptyResponse|CreateUsersWithListInput200ApplicationJsonResponse;
}
