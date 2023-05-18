<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class GetClient201ApplicationJsonSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,
    ) {
    }
}
