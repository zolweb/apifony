<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    /**
     * OperationId: findPetsByStatus
     *
     * Finds Pets by status
     *
     * Multiple status values can be provided with comma separated strings
     */
    public function handle(
        ?string $status = 'available',
    ): FindPetsByStatus200ApplicationJsonResponse|FindPetsByStatus400EmptyResponse;
}
