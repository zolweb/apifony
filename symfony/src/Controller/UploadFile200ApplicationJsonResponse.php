<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UploadFile200ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        ApiResponseSchema $payload,
    ) {
        parent::__construct(
            $payload,
            200,
            [],
        );
    }
}