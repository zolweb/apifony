<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    /**
     * OperationId: updatePetWithForm
     */
    public function handle(
        ?mixed $qName,
        ?mixed $pPetId,
        ?mixed $qStatus,
    ): ;
}
