<?php

namespace Zol\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Pagination
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $recordsCount,

        #[Assert\NotNull]
        public readonly int $pagesCount,

        public readonly ?int $currentPage,

        public readonly ?int $previousPage,

        public readonly ?int $nextPage,
    ) {
    }
}
