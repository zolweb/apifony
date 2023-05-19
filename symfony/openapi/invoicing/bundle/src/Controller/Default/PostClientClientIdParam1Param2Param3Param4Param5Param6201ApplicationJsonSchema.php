<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format as AssertFormat;

class PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,
    ) {
    }
}
