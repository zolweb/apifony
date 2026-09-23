<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

use Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation\RawOperation200Response;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation\RawOperation200ResponsePayload;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation\RawOperationHandler;

class RawHandler implements RawOperationHandler
{
    public function rawOperation(mixed $qRawParam, mixed $requestBodyPayload): RawOperation200Response
    {
        return new RawOperation200Response(
            new RawOperation200ResponsePayload($requestBodyPayload, $qRawParam),
        );
    }
}
