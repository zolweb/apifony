<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetInventory200ApplicationJsonResponse extends Response
{
    public function __construct(
        GetInventory200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}