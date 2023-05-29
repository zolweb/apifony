<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int32 as AssertInt32;

class ApiResponse
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt32]
        public readonly int $code,

        #[Assert\NotNull]
        public readonly string $type,

        #[Assert\NotNull]
        public readonly string $message,
    ) {
    }
}
