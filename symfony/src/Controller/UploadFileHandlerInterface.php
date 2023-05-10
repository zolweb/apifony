<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    /**
     * OperationId: uploadFile
     *
     * uploads an image
     *
     * desc
     */
    public function handle(
        ?string $qAdditionalMetadata,
        int $pPetId,
    ): UploadFile200ApplicationJsonResponse;
}