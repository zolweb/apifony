<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class ApiResponseSchema
{
    /**
    */
    public function __construct(
        #[Int32()]
        public readonly ?int $code = null,
        public readonly ?string $type = null,
        public readonly ?string $message = null,
    ) {
    }
}