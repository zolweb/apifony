<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

interface PetHandler
{
    public function updatePetFromUpdatePetApplicationJsonMediaTypeSchemaPayloadToApplicationJsonContent(
        UpdatePetApplicationJsonMediaTypeSchema $requestBodyPayload,
    ):
        UpdatePet200ApplicationJson;
    public function updatePetFromUpdatePetApplicationJsonMediaTypeSchemaPayloadToEmptyContent(
        UpdatePetApplicationJsonMediaTypeSchema $requestBodyPayload,
    ):
        UpdatePet400Empty |
        UpdatePet404Empty |
        UpdatePet405Empty;

    public function addPetFromPetPayloadToApplicationJsonContent(
        Pet $requestBodyPayload,
    ):
        AddPet200ApplicationJson;
    public function addPetFromPetPayloadToEmptyContent(
        Pet $requestBodyPayload,
    ):
        AddPet405Empty;

    public function findPetsByStatusFromEmptyPayloadToApplicationJsonContent(
        string $qStatus,
    ):
        FindPetsByStatus200ApplicationJson;
    public function findPetsByStatusFromEmptyPayloadToEmptyContent(
        string $qStatus,
    ):
        FindPetsByStatus400Empty;

    public function findPetsByTagsFromEmptyPayloadToApplicationJsonContent(
        string $qTags,
    ):
        FindPetsByTags200ApplicationJson;
    public function findPetsByTagsFromEmptyPayloadToEmptyContent(
        string $qTags,
    ):
        FindPetsByTags400Empty;

    public function getPetByIdFromEmptyPayloadToApplicationJsonContent(
        int $pPetId,
    ):
        GetPetById200ApplicationJson;
    public function getPetByIdFromEmptyPayloadToEmptyContent(
        int $pPetId,
    ):
        GetPetById400Empty |
        GetPetById404Empty;

    public function updatePetWithFormFromEmptyPayloadToEmptyContent(
        string $qName,
        int $pPetId,
        string $qStatus,
    ):
        UpdatePetWithForm204Empty |
        UpdatePetWithForm405Empty;

    public function deletePetFromEmptyPayloadToEmptyContent(
        string $hApi_key,
        int $pPetId,
    ):
        DeletePet200Empty |
        DeletePet400Empty;

    public function uploadFileFromEmptyPayloadToApplicationJsonContent(
        string $qAdditionalMetadata,
        int $pPetId,
    ):
        UploadFile200ApplicationJson;
}
