<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

use App\Zol\Invoicing\Presentation\Api\Bundle\Model\Pet;

interface PetHandler
{
    public function UpdatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        UpdatePetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdatePet200ApplicationJsonResponse;

    public function UpdatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToContent(
        UpdatePetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdatePet400EmptyResponse |
        UpdatePet404EmptyResponse |
        UpdatePet405EmptyResponse;

    public function AddPetFromPetPayloadToApplicationJsonContent(
        Pet $requestBodyPayload,
    ):
        AddPet200ApplicationJsonResponse;

    public function AddPetFromPetPayloadToContent(
        Pet $requestBodyPayload,
    ):
        AddPet405EmptyResponse;

    public function FindPetsByStatusFromEmptyPayloadToApplicationJsonContent(
        string $qStatus,
    ):
        FindPetsByStatus200ApplicationJsonResponse;

    public function FindPetsByStatusFromEmptyPayloadToContent(
        string $qStatus,
    ):
        FindPetsByStatus400EmptyResponse;

    public function FindPetsByTagsFromEmptyPayloadToApplicationJsonContent(
        string $qTags,
    ):
        FindPetsByTags200ApplicationJsonResponse;

    public function FindPetsByTagsFromEmptyPayloadToContent(
        string $qTags,
    ):
        FindPetsByTags400EmptyResponse;

    public function GetPetByIdFromEmptyPayloadToApplicationJsonContent(
        int $pPetId,
    ):
        GetPetById200ApplicationJsonResponse;

    public function GetPetByIdFromEmptyPayloadToContent(
        int $pPetId,
    ):
        GetPetById400EmptyResponse |
        GetPetById404EmptyResponse;

    public function UpdatePetWithFormFromEmptyPayloadToContent(
        int $pPetId,
        string $qName,
        string $qStatus,
    ):
        UpdatePetWithForm204EmptyResponse |
        UpdatePetWithForm405EmptyResponse;

    public function DeletePetFromEmptyPayloadToContent(
        string $hApi_key,
        int $pPetId,
    ):
        DeletePet200EmptyResponse |
        DeletePet400EmptyResponse;

    public function UploadFileFromEmptyPayloadToApplicationJsonContent(
        int $pPetId,
        string $qAdditionalMetadata,
    ):
        UploadFile200ApplicationJsonResponse;
}
