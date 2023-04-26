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
        int $petId,
        ?string $additionalMetadata,
    ): UploadFile200ApplicationJsonResponse;
}
