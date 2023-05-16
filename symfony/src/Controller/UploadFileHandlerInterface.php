<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    public function handle(
        string $qAdditionalMetadata,
        int $pPetId,
        Lol $payload,
    ) ;
}
