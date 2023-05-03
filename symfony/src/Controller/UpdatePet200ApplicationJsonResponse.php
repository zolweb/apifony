<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class UpdatePet200ApplicationJsonResponse extends Response
{
    public function __construct(
        UpdatePet200ApplicationJsonResponsePayloadUpdatePet200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}