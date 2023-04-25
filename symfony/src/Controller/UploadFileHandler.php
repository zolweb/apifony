<?php

namespace App\Controller;

interface UploadFileHandler
{
    public function handle(
        int $petId,
        ?string $additionalMetadata,
    ): void
}}
