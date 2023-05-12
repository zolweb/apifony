<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    /**
     * OperationId: uploadFile
     */
    public function handle(
        ?mixed $qAdditionalMetadata,
        ?mixed $pPetId,
    ): ;
}
