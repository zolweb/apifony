<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentRefOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
interface ComponentRefOperationHandler
{
    /**
     * @param list<string> $qStringListParam
     * @param list<Abc> $qAbcListParam
     */
    public function componentRefOperation(array $qStringListParam, array $qAbcListParam): ComponentRefOperation200Response;
}