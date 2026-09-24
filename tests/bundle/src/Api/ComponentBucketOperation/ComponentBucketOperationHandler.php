<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentBucketOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
interface ComponentBucketOperationHandler
{
    public function componentBucketOperation(string $pBucketPathParam, Abc $qBucketQueryParam, Abc $requestBodyPayload): ComponentBucketOperation200Response;
}