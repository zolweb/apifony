<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class LoginUser200ApplicationJsonResponse extends Response
{
    public function __construct(
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}