<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    /**
     * OperationId: uploadFile
     */
    public function handle(
        int $pPetId
        ?string $qAdditionalMetadata = ''
    ): ;
}
