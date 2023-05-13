<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    /**
     * OperationId: findPetsByStatus
     */
    public function handle(
        ?string $status = 'available'
    ): ;
}
