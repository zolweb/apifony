<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AddPet200ApplicationJsonResponse extends JsonResponse
{
    public function __construct(
        PetSchema $payload,
    ) {
        parent::__construct(
            $payload,
            200,
            [],
        );
    }
}