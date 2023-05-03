<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class CreateUser201ApplicationJsonResponse extends Response
{
    public function __construct(
        UserSchema $payload,
    ) {
        parent::__construct(
            '',
            201,
            [],
        );
    }
}