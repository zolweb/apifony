<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    /**
     * OperationId: uploadFile
     */
    public function handle(
        ?string $additionalMetadata = ''
        int $petId
    ): ;
}
