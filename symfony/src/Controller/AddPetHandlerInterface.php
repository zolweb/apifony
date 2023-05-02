<?php

namespace App\Controller;

interface AddPetHandlerInterface
{
    /**
     * OperationId: addPet
     *
     * Add a new pet to the store
     *
     * Add a new pet to the store
     */
    public function handle(
        PetSchema $payload,
    ): AddPet200ApplicationJsonResponse|AddPet405EmptyResponse;
}