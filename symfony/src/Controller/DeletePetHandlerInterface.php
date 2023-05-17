<?php

namespace App\Controller;

interface DeletePetHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
        string $hApi_key,
        int $pPetId,
    ):
        DeletePet200Empty |
        DeletePet400Empty;
}
