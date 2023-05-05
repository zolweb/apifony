<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CreateUser201ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        UserSchema $payload,
    ) {
        parent::__construct(
            $payload,
            201,
            [],
        );
    }
}