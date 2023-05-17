<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class ApiResponse
{
    public function __construct(
        #[Int32]
        public readonly int $code,

        public readonly string $type,

        public readonly string $message,
    ) {
    }
}
