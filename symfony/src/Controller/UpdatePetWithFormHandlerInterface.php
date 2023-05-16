<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    public function handle(
        string $qName,
        int $pPetId,
        string $qStatus,
    ) ;
}
