<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    public function handle(
        string $hApi_key,
        int $pPetId,
    ) ;
}
