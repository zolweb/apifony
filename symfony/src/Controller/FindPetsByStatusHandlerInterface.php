<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    public function handle(
        ?string $qStatus = 'available'
    ): ;
}
