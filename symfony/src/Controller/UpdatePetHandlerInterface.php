<?php

namespace App\Controller;

interface UpdatePetHandlerInterface
{
    /**
     * OperationId: updatePet
     *
     * Update an existing pet
     *
     * Update an existing pet by Id
     */
    public function handle(
        PetSchema $dto,
    ): UpdatePet200ApplicationJsonResponse|UpdatePet400EmptyResponse|UpdatePet404EmptyResponse|UpdatePet405EmptyResponse;
}
