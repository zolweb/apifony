<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetUserByName200ApplicationXmlResponse extends Response
{
    public function __construct(
        GetUserByName200ApplicationXmlResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}