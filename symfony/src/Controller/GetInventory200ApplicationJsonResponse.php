<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetInventory200ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        GetInventory200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            $payload,
            200,
            [],
        );
    }
}