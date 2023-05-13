<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    /**
     * OperationId: deletePet
     */
    public function handle(
        ?mixed $hApi_key,
        ?mixed $pPetId,
    ): ;
}
