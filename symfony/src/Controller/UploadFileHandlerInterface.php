<?php

namespace App\Controller;

interface UploadFileHandlerInterface
{
    public function handle(
        int $pPetId
        ?string $qAdditionalMetadata = ''
        Lol $payload,
    ) ;
}
