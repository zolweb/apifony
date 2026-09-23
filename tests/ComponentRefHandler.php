<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

use Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentRefOperation\ComponentRefOperation200Response;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentRefOperation\ComponentRefOperation200ResponsePayload;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentRefOperation\ComponentRefOperationHandler;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;

class ComponentRefHandler implements ComponentRefOperationHandler
{
    /**
     * @param list<string> $qStringListParam
     * @param list<Abc>    $qAbcListParam
     */
    public function componentRefOperation(array $qStringListParam, array $qAbcListParam): ComponentRefOperation200Response
    {
        return new ComponentRefOperation200Response(
            new ComponentRefOperation200ResponsePayload($qStringListParam, $qAbcListParam),
        );
    }
}
