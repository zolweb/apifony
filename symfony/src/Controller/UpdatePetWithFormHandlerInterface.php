<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    /**
     * OperationId: updatePetWithForm
     *
     * Updates a pet in the store with form data
     *
     * desc
     */
    public function handle(
        ?string $qName,
        int $pPetId,
        ?string $qStatus,
    ): UpdatePetWithForm204EmptyResponse|UpdatePetWithForm405EmptyResponse;
}