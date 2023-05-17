<?php

namespace App\Controller;

interface UpdatePetWithFormHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
        string $qName,
        int $pPetId,
        string $qStatus,
    ):
        UpdatePetWithForm204Empty |
        UpdatePetWithForm405Empty;
}
