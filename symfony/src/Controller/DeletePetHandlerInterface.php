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
        ?string $hApi_key,
        int $pPetId,
    ): DeletePet400EmptyResponse;
}