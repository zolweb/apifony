<?php

namespace App\Controller;

interface AddPetHandlerInterface
{
    public function handlePetPayloadToApplicationJsonContent(
        Pet $requestBodyPayload,
    ):
        AddPet200ApplicationJson;
    public function handlePetPayloadToEmptyContent(
        Pet $requestBodyPayload,
    ):
        AddPet405Empty;
}
