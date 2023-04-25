<?php

namespace App\Controller;

interface DeletePetHandler
{
    public function handle(
        ?string $api_key,
        int $petId,
    ): void;
}
