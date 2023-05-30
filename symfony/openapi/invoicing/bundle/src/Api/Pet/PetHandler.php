<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

interface PetHandler
{


    public function FindPetsByStatusFromEmptyPayloadToContent(
        string $qstatus,
    ):
        FindPetsByStatus200EmptyResponse |
        FindPetsByStatus400EmptyResponse;

    public function FindPetsByTagsFromEmptyPayloadToContent(
        string $qtags,
    ):
        FindPetsByTags200EmptyResponse |
        FindPetsByTags400EmptyResponse;

    public function GetPetByIdFromEmptyPayloadToContent(
        int $ppetId,
    ):
        GetPetById200EmptyResponse |
        GetPetById400EmptyResponse |
        GetPetById404EmptyResponse;

    public function UpdatePetWithFormFromEmptyPayloadToContent(
        int $ppetId,
        string $qname,
        string $qstatus,
    ):
        UpdatePetWithForm204EmptyResponse |
        UpdatePetWithForm405EmptyResponse;

    public function DeletePetFromEmptyPayloadToContent(
        string $hapiKey,
        int $ppetId,
    ):
        DeletePet200EmptyResponse |
        DeletePet400EmptyResponse;

    public function UploadFileFromEmptyPayloadToContent(
        int $ppetId,
        string $qadditionalMetadata,
    ):
        UploadFile200EmptyResponse;
}
