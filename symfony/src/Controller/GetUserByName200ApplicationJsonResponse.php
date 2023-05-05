<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetUserByName200ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        UserSchema $payload,
    ) {
        parent::__construct(
            $payload,
            200,
            [],
        );
    }
}