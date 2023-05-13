<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Tag
{
    public function __construct(
        #[Lol()]
        public readonly ?int $id,
        public readonly ?string $name,
    ) {
    }
}
