<?php

namespace App\Handler;

class AddPetHandler implements \App\Controller\AddPetHandler
{
    public function handle($dto)
    {
        var_dump($dto);
    }
}