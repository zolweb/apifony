<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetClient201ApplicationJsonResponsePayload
{
    /**
    */
    public function __construct(
        public readonly ?string $id = null,
    ) {
    }
}