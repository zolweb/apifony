<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64 as AssertInt64;

class Category
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        public readonly int $id,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
