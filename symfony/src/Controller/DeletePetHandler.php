<?php

namespace App\Controller;

interface DeletePetHandler
{
    public function handle(
        int $petId,
    );
}
