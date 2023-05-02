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
        int $pPetId = null,
        ?string $qAdditionalMetadata = null,
    ): UploadFile200ApplicationJsonResponse;
}