<?php

namespace App\Controller;

interface GetPetByIdHandlerInterface
{
    /**
     * OperationId: getPetById
     *
     * Find pet by ID
     *
     * Returns a single pet
     */
    public function handle(
        int $pPetId = null,
    ): GetPetById200ApplicationJsonResponse|GetPetById400EmptyResponse|GetPetById404EmptyResponse;
}