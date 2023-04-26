<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    /**
     * OperationId: updatePetWithForm
     *
     * Updates a pet in the store with form data
     */
    public function handle(
        int $petId,
        ?string $name,
        ?string $status,
    ): UpdatePetWithForm405EmptyResponse;
}
