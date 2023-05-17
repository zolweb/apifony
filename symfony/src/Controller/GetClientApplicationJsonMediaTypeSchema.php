<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetClientApplicationJsonMediaTypeSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,
    ) {
    }
}
