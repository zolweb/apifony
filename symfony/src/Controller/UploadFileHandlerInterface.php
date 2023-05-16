<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    public function handleNullApplicationJson(
            string $qAdditionalMetadata,
            int $pPetId,
    );
}
