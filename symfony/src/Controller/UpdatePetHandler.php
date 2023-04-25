<?php

namespace App\Controller;

interface UpdatePetHandler
{
    public function handle(
        PetSchema $dto,
    ): UpdatePet200ApplicationJsonResponse|UpdatePet400EmptyResponse|UpdatePet404EmptyResponse|UpdatePet405EmptyResponse;
}
