<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class Tag
{
    public function __construct(
        public readonly int $id,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
