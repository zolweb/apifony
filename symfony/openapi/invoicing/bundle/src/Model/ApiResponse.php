<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int32 as AssertInt32;

class ApiResponse
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt32]
        #[Assert\Choice(choices: [
        ])]
        public readonly int $code,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $type,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $message,
    ) {
    }
}
