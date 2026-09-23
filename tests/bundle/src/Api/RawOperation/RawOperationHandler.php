<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation;

interface RawOperationHandler
{
    public function rawOperation(mixed $qRawParam, mixed $requestBodyPayload): RawOperation200Response;
}