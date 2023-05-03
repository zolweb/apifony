<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    /**
     * OperationId: uploadFile
     *
     * uploads an image
     */
    public function handle(
        int $pPetId,
        ?string $qAdditionalMetadata,
    ): UploadFile200ApplicationJsonResponse;
}