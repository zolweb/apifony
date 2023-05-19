<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format as AssertFormat;

class Tag
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertFormat\Int64]
        public readonly int $id,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
