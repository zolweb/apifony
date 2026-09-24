<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

use Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentBucketOperation\ComponentBucketOperation200Response;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentBucketOperation\ComponentBucketOperationHandler;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;

/**
 * Covers the operation whose parameters, request body, response and one of its headers are all
 * written as references into the components buckets other than schemas.
 */
class ComponentBucketHandler implements ComponentBucketOperationHandler
{
    public function componentBucketOperation(string $pBucketPathParam, Abc $qBucketQueryParam, Abc $requestBodyPayload): ComponentBucketOperation200Response
    {
        return new ComponentBucketOperation200Response(
            new Abc("{$pBucketPathParam}|{$qBucketQueryParam->def}|{$requestBodyPayload->def}"),
            $pBucketPathParam,
            $qBucketQueryParam->def,
        );
    }
}
