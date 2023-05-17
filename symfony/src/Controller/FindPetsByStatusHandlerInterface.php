<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    public function handleEmptyApplicationJson(
        string $qStatus,
    ):
        FindPetsByStatus200PetArray;
}
