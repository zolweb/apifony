<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    public function handleNullApplicationJson(
            string $qStatus,
    );
}
