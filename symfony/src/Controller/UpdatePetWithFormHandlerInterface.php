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
        int $pPetId = null,
        ?string $qName = null,
        ?string $qStatus = null,
    ): UpdatePetWithForm405EmptyResponse;
}