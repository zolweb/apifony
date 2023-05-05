<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PlaceOrder200ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        OrderSchema $payload,
    ) {
        parent::__construct(
            $payload,
            200,
            [],
        );
    }
}