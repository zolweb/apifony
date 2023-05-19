<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format as AssertFormat;

class ApiResponse
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertFormat\Int32]
        public readonly int $code,

        #[Assert\NotNull]
        public readonly string $type,

        #[Assert\NotNull]
        public readonly string $message,
    ) {
    }
}
