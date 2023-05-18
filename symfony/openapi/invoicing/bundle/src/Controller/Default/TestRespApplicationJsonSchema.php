<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class TestRespApplicationJsonSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $test,
    ) {
    }
}
