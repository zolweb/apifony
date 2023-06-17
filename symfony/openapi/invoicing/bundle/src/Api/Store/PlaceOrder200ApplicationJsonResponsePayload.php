<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64 as AssertInt64;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int32 as AssertInt32;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\DateTime as AssertDateTime;

class PlaceOrder200ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        public readonly int $id,

        #[Assert\NotNull]
        #[AssertInt64]
        public readonly int $petId,

        #[Assert\NotNull]
        #[AssertInt32]
        #[Assert\GreaterThan(value: 5)]
        public readonly int $quantity,

        #[Assert\NotNull]
        #[AssertDateTime]
        public readonly string $shipDate,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
            'placed',
            'approved',
            'delivered',
        ])]
        public readonly string $status,

        #[Assert\NotNull]
        public readonly bool $complete,
    ) {
    }
}
