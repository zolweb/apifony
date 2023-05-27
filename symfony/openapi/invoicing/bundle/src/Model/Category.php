<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64 as AssertInt64;

class Category
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        #[Assert\Choice(choices: [
        ])]
        public readonly int $id,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $name,
    ) {
    }
}
