<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetClient201ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        GetClient201ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            $payload,
            201,
            [],
        );
    }
}