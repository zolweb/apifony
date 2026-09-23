<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentRefOperation;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
class ComponentRefOperation200ResponsePayload
{
    /**
     * @param list<string> $stringListEcho
     * @param list<Abc> $abcListEcho
     */
    public function __construct(
        
        #[Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull()])]
        public readonly array $stringListEcho,
        
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\NotNull()])]
        public readonly array $abcListEcho
    )
    {
    }
}