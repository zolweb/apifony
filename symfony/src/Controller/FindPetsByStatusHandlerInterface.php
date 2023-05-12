<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    /**
     * OperationId: findPetsByStatus
     */
    public function handle(
        ?mixed $qStatus = 'available',
    ): ;
}
