<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation;

interface RawShapesOperationHandler
{
    public function rawShapesOperation(mixed $qTypelessParam, mixed $qStringParam, RawShapesOperationRequestBodyPayload $requestBodyPayload): RawShapesOperation200Response;
}