<?php

namespace App\Controller;

interface GetPetByIdHandlerInterface
{
    public function handleEmptyApplicationJson(
        int $pPetId,
    ):
        GetPetById200Pet;
}
