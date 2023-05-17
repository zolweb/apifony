<?php

namespace App\Controller;

interface UpdatePetHandlerInterface
{
    public function handleUpdatePetApplicationJsonMediaTypeSchemaPayloadToApplicationJsonContent(
        UpdatePetApplicationJsonMediaTypeSchema $requestBodyPayload,
    ):
        UpdatePet200ApplicationJson;
    public function handleUpdatePetApplicationJsonMediaTypeSchemaPayloadToEmptyContent(
        UpdatePetApplicationJsonMediaTypeSchema $requestBodyPayload,
    ):
        UpdatePet400Empty |
        UpdatePet404Empty |
        UpdatePet405Empty;
}
