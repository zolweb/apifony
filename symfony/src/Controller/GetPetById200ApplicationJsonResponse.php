<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetPetById200ApplicationJsonResponse extends Response
{
    public function __construct(
        GetPetById200ApplicationJsonResponsePayloadGetPetById200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}