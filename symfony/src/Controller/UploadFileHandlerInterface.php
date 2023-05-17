<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        string $qAdditionalMetadata,
        int $pPetId,
    ):
        UploadFile200ApplicationJson;
}
