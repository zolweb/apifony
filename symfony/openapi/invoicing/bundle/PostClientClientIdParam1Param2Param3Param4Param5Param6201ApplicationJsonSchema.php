<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,
    ) {
    }
}
