<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class ApiResponse
{
    public function __construct(
        public readonly int $code,

        #[Assert\NotNull]
        public readonly string $type,

        #[Assert\NotNull]
        public readonly string $message,
    ) {
    }
}
