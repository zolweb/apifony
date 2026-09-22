<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Model;

use Symfony\Component\Validator\Constraints as Assert;
class Node
{
    /**
     * @param string $name
     * @param list<Node> $children
     */
    public function __construct(
        
        #[Assert\NotNull]
        public readonly string $name,
        
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $children = []
    )
    {
    }
}