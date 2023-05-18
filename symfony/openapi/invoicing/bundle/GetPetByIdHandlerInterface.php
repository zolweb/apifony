<?php

namespace App\Controller;

interface GetPetByIdHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        int $pPetId,
    ):
        GetPetById200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        int $pPetId,
    ):
        GetPetById400Empty |
        GetPetById404Empty;
}
