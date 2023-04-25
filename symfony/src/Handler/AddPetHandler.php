<?php

namespace App\Handler;

class AddPetHandler implements \App\Controller\AddPetHandler
{
    public function handle($dto): void
    {
        var_dump($dto);
    }
}