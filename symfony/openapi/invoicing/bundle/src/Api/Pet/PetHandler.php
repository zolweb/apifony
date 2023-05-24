<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

interface PetHandler
{
    public function UpdatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        UpdatePetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdatePet200ApplicationJson;

    public function UpdatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToContent(
        UpdatePetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdatePet400Empty |
        UpdatePet404Empty |
        UpdatePet405Empty;

    public function AddPetFromAddPetApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        AddPetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        AddPet200ApplicationJson;

    public function AddPetFromAddPetApplicationJsonRequestBodyPayloadPayloadToContent(
        AddPetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        AddPet405Empty;

    public function FindPetsByStatusFromEmptyPayloadToApplicationJsonContent(
        string $qstatus,
    ):
        FindPetsByStatus200ApplicationJson;

    public function FindPetsByStatusFromEmptyPayloadToContent(
        string $qstatus,
    ):
        FindPetsByStatus400Empty;

    public function FindPetsByTagsFromEmptyPayloadToApplicationJsonContent(
        string $qtags,
    ):
        FindPetsByTags200ApplicationJson;

    public function FindPetsByTagsFromEmptyPayloadToContent(
        string $qtags,
    ):
        FindPetsByTags400Empty;

    public function GetPetByIdFromEmptyPayloadToApplicationJsonContent(
        int $ppetId,
    ):
        GetPetById200ApplicationJson;

    public function GetPetByIdFromEmptyPayloadToContent(
        int $ppetId,
    ):
        GetPetById400Empty |
        GetPetById404Empty;

    public function UpdatePetWithFormFromEmptyPayloadToContent(
        int $ppetId,
        string $qname,
        string $qstatus,
    ):
        UpdatePetWithForm204Empty |
        UpdatePetWithForm405Empty;

    public function DeletePetFromEmptyPayloadToContent(
        string $hapiKey,
        int $ppetId,
    ):
        DeletePet200Empty |
        DeletePet400Empty;

    public function UploadFileFromEmptyPayloadToApplicationJsonContent(
        int $ppetId,
        string $qadditionalMetadata,
    ):
        UploadFile200ApplicationJson;
}
