<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    public function handleEmptyApplicationJson(
        string $qAdditionalMetadata,
        int $pPetId,
    ):
        UploadFile200ApiResponse;
}
