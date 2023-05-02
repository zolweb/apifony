<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class CreateUsersWithListInput200ApplicationJsonResponse extends Response
{
    public function __construct(
        UserSchemaCreateUsersWithListInput200ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}