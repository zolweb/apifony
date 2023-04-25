<?php

namespace App\Controller;

interface GetPetByIdHandler
{
    public function handle(
        int $petId,
    ): GetPetById200ApplicationJsonResponse|GetPetById400EmptyResponse|GetPetById404EmptyResponse;
}
