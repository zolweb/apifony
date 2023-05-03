<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class AddPet200ApplicationJsonResponse extends Response
{
    public function __construct(
        AddPet200ApplicationJsonResponsePayloadAddPet200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}