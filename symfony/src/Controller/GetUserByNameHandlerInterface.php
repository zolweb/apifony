<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    /**
     * OperationId: getUserByName
     *
     * Get user by user name
     *
     * desc
     */
    public function handle(
        string $pUsername,
    ): GetUserByName200ApplicationJsonResponse|GetUserByName200ApplicationXmlResponse|GetUserByName400EmptyResponse|GetUserByName404EmptyResponse;
}