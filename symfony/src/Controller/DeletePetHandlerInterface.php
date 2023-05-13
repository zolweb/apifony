<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    /**
     * OperationId: deletePet
     */
    public function handle(
        int $pPetId
        ?string $hApi_key = ''
    ): ;
}
