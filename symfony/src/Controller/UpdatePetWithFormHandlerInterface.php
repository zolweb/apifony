<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    /**
     * OperationId: updatePetWithForm
     */
    public function handle(
        ?string $name = ''
        int $petId
        ?string $status = ''
    ): ;
}
