<?php

namespace App\Controller;

interface GetPetByIdHandler
{
    public function handle(
        int $petId,
    );
}
