<?php

namespace App\Controller;

interface GetPetByIdHandlerInterface
{
    /**
     * OperationId: getPetById
     */
    public function handle(
        ?mixed $pPetId,
    ): ;
}
