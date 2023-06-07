<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

interface PetHandler
{


    public function FindPetsByStatusFromEmptyPayloadToContent(
        string $qStatus,
    ):
        FindPetsByStatus200EmptyResponse |
        FindPetsByStatus400EmptyResponse;

    public function FindPetsByTagsFromEmptyPayloadToContent(
        string $qTags,
    ):
        FindPetsByTags200EmptyResponse |
        FindPetsByTags400EmptyResponse;

    public function GetPetByIdFromEmptyPayloadToContent(
        int $pPetId,
    ):
        GetPetById200EmptyResponse |
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

    public function UploadFileFromEmptyPayloadToContent(
        int $pPetId,
        string $qAdditionalMetadata,
    ):
        UploadFile200EmptyResponse;
}
