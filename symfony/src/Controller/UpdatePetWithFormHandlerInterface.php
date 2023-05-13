<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    /**
     * OperationId: updatePetWithForm
     */
    public function handle(
        int $pPetId
        ?string $qName = ''
        ?string $qStatus = ''
    ): ;
}
