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
        int $petId = null,
        ?string $additionalMetadata = null,
    ): UploadFile200ApplicationJsonResponse;
}
