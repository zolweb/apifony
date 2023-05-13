<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class 645f3da861637
{
    /**
     * object
     * object
    */
    public function __construct(
        #[Lol()]
        public readonly ?int $id,
        public readonly ?string $name,
        #[Assert\Valid()]
        public readonly ?Lol $category,
        public readonly ?array $photoUrls,
        #[Assert\Unique()]
        public readonly ?array $tags,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public readonly ?string $status,
    ) {
    }
}
