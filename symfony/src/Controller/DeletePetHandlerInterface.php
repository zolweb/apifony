<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    /**
     * OperationId: deletePet
     *
     * Deletes a pet
     *
     * delete a pet
     */
    public function handle(
        ?string $api_key,
        int $petId,
    ): DeletePet400EmptyResponse;
}
