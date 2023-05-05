<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginUser200ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
    ) {
        parent::__construct(
            $payload,
            200,
            [],
        );
    }
}