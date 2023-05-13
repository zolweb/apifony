<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    public function handle(
        int $pPetId
        ?string $hApi_key = ''
    ): ;
}
