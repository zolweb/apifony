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
        PetSchema $dto,
    ): AddPet200ApplicationJsonResponse|AddPet405EmptyResponse;
}
