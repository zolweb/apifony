<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Format\Email as AssertEmail;
class FirstOperationQueryParamObjectNestedObjectProperty
{
    /**
     * @param string $emailProperty
     */
    public function __construct(
        
        #[Assert\NotNull]
        #[AssertEmail]
        public readonly string $emailProperty
    )
    {
    }
}