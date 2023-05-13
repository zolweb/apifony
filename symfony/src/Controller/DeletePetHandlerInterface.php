<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    /**
     * OperationId: deletePet
     */
    public function handle(
        ?string $api_key
        int $petId
    ): ;
}
