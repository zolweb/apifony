<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

use Symfony\Component\Validator\Constraints as Assert;

class PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,
    ) {
    }
}
