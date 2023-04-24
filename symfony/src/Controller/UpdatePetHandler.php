<?php

namespace App\Controller;

interface UpdatePetHandler
{
    public function handle(
        PetSchema $dto,
    );
}
