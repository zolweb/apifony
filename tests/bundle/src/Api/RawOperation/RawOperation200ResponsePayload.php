<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation;

use Symfony\Component\Validator\Constraints as Assert;
class RawOperation200ResponsePayload
{
    /**
     * @param mixed $bodyEcho
     * @param mixed $paramEcho
     */
    public function __construct(
        
        #[Assert\NotNull]
        public readonly mixed $bodyEcho,
        
        #[Assert\NotNull]
        public readonly mixed $paramEcho
    )
    {
    }
}