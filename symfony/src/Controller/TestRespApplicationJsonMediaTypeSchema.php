<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class TestRespApplicationJsonMediaTypeSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $test,
    ) {
    }
}
