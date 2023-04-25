<?php

namespace App\Controller;

interface UpdatePetWithFormHandler
{
    public function handle(
        int $petId,
        ?string $name,
        ?string $status,
    );
}
