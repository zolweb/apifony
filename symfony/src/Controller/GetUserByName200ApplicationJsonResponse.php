<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetUserByName200ApplicationJsonResponse extends Response
{
    public function __construct(
        GetUserByName200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}