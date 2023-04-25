<?php

namespace App\Controller;

interface AddPetHandler
{
    public function handle(
        PetSchema $dto,
    ): AddPet200ApplicationJsonResponse|AddPet405EmptyResponse;
}
