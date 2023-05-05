<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetUserByName200ApplicationXmlResponse extends Response
{
    public function __construct(
        UserSchema $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}