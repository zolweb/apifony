<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    /**
     * OperationId: getUserByName
     */
    public function handle(
        string $username
    ): ;
}
