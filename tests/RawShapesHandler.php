<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

use Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation\RawShapesOperation200Response;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation\RawShapesOperation200ResponsePayload;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation\RawShapesOperationHandler;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation\RawShapesOperationRequestBodyPayload;

class RawShapesHandler implements RawShapesOperationHandler
{
    public function rawShapesOperation(mixed $qTypelessParam, mixed $qStringParam, RawShapesOperationRequestBodyPayload $requestBodyPayload): RawShapesOperation200Response
    {
        return new RawShapesOperation200Response(
            new RawShapesOperation200ResponsePayload(
                $qTypelessParam,
                $qStringParam,
                $requestBodyPayload->nullableRaw,
                $requestBodyPayload->refRaw,
            ),
        );
    }
}
