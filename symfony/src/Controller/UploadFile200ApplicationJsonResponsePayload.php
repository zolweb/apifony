<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class UploadFile200ApplicationJsonResponsePayload
{
    /**
    */
    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $type = null,
        public readonly ?string $message = null,
    ) {
    }
}