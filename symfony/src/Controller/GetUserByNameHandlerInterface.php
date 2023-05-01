<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    /**
     * OperationId: getUserByName
     *
     * Get user by user name
     */
    public function handle(
        string $pUsername = null,
    ): GetUserByName200ApplicationJsonResponse|GetUserByName200ApplicationXmlResponse|GetUserByName400EmptyResponse|GetUserByName404EmptyResponse;
}
