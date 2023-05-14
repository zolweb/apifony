<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    public function handle(
        int $pPetId
        ?string $qName = ''
        ?string $qStatus = ''
    ) ;
}
