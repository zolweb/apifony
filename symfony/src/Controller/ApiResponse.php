<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class ApiResponse
{
    public function __construct(
        #[Assert\NotNull]
        #[Int32]
        public readonly int $code,

        #[Assert\NotNull]
        public readonly string $type,

        #[Assert\NotNull]
        public readonly string $message,
    ) {
    }
}
