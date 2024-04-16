<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Locale
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $id,
    ) {
    }
}
