<?php

namespace AppZolInvoicingPresentationApiBundle\Api\Pet;

use AppZolInvoicingPresentationApiBundle\Model\Pet;
use AppZolInvoicingPresentationApiBundle\Model\ApiResponse;

interface PetHandler
{
    public function updatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        UpdatePetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdatePet200ApplicationJsonResponse;

    public function updatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToContent(
        UpdatePetApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdatePet400EmptyResponse |
        UpdatePet404EmptyResponse |
        UpdatePet405EmptyResponse;

    public function addPetFromPetPayloadToApplicationJsonContent(
        Pet $requestBodyPayload,
    ):
        AddPet200ApplicationJsonResponse;

    public function addPetFromPetPayloadToContent(
        Pet $requestBodyPayload,
    ):
        AddPet405EmptyResponse;

    public function findPetsByStatusFromEmptyPayloadToContent(
        string $qStatus,
    ):
        FindPetsByStatus200EmptyResponse |
        FindPetsByStatus400EmptyResponse;

    public function findPetsByTagsFromEmptyPayloadToContent(
        string $qTags,
    ):
        FindPetsByTags200EmptyResponse |
        FindPetsByTags400EmptyResponse;

    public function getPetByIdFromEmptyPayloadToApplicationJsonContent(
        int $pPetId,
    ):
        GetPetById200ApplicationJsonResponse;

    public function getPetByIdFromEmptyPayloadToContent(
        int $pPetId,
    ):
        GetPetById400EmptyResponse |
        GetPetById404EmptyResponse;

    public function updatePetWithFormFromEmptyPayloadToContent(
        int $pPetId,
        string $qName,
        string $qStatus,
    ):
        UpdatePetWithForm204EmptyResponse |
        UpdatePetWithForm405EmptyResponse;

    public function deletePetFromEmptyPayloadToContent(
        string $hApi_key,
        int $pPetId,
    ):
        DeletePet200EmptyResponse |
        DeletePet400EmptyResponse;

    public function uploadFileFromEmptyPayloadToApplicationJsonContent(
        int $pPetId,
        string $qAdditionalMetadata,
    ):
        UploadFile200ApplicationJsonResponse;
}
