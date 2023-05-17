<?php

namespace App\Controller;

interface AddPetHandlerInterface
{
    public function handlePetApplicationJson(
        Pet $content,
    ):
        AddPet200Pet;
}
