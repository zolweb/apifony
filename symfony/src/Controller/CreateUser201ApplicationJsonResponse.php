<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class CreateUser201ApplicationJsonResponse extends Response
{
    public function __construct(
        UserSchemaCreateUser201ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            201,
            [],
        );
    }
}