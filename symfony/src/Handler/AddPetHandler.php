<?php

namespace App\Handler;

use App\Controller\AddPet405EmptyResponse;

class AddPetHandler implements \App\Controller\AddPetHandler
{
    public function handle($dto): \App\Controller\AddPet200ApplicationJsonResponse|\App\Controller\AddPet405EmptyResponse
    {
        var_dump($dto);

        return new AddPet405EmptyResponse();
    }
}