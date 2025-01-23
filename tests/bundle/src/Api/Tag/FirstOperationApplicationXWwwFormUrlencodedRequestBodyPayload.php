<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Tag;

use Symfony\Component\Validator\Constraints as Assert;

class FirstOperationApplicationXWwwFormUrlencodedRequestBodyPayload
{
    public function __construct(
        #[Assert\NotNull] public readonly string $stringProperty,
        #[Assert\NotNull] public readonly float $numberProperty,
        #[Assert\NotNull] public readonly int $integerProperty,
        #[Assert\NotNull] public readonly bool $booleanProperty,
        #[Assert\NotNull] public readonly string $string_property
    ) {
    }
}
