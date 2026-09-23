<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Format\Email as AssertEmail;
class FirstOperationQueryParamObjectNestedObjectProperty
{
    /**
     * @param string $emailProperty
     */
    public function __construct(
        
        #[AssertEmail]
        public readonly string $emailProperty
    )
    {
    }
}