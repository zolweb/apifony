<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            $payload,
            201,
            [],
        );
    }
}