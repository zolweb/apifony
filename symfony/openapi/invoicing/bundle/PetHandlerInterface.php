<?php

namespace App\Controller;

interface PetHandlerInterface
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

    public function handlePetPayloadToApplicationJsonContent(
        Pet $requestBodyPayload,
    ):
        AddPet200ApplicationJson;
    public function handlePetPayloadToEmptyContent(
        Pet $requestBodyPayload,
    ):
        AddPet405Empty;

    public function handleEmptyPayloadToApplicationJsonContent(
        string $qStatus,
    ):
        FindPetsByStatus200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $qStatus,
    ):
        FindPetsByStatus400Empty;

    public function handleEmptyPayloadToApplicationJsonContent(
        string $qTags,
    ):
        FindPetsByTags200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $qTags,
    ):
        FindPetsByTags400Empty;

    public function handleEmptyPayloadToApplicationJsonContent(
        int $pPetId,
    ):
        GetPetById200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        int $pPetId,
    ):
        GetPetById400Empty |
        GetPetById404Empty;

    public function handleEmptyPayloadToEmptyContent(
        string $qName,
        int $pPetId,
        string $qStatus,
    ):
        UpdatePetWithForm204Empty |
        UpdatePetWithForm405Empty;

    public function handleEmptyPayloadToEmptyContent(
        string $hApi_key,
        int $pPetId,
    ):
        DeletePet200Empty |
        DeletePet400Empty;

    public function handleEmptyPayloadToApplicationJsonContent(
        string $qAdditionalMetadata,
        int $pPetId,
    ):
        UploadFile200ApplicationJson;
}
