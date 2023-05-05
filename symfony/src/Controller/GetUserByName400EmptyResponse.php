<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetUserByName400EmptyResponse extends Response
{
    public function __construct(
    ) {
        parent::__construct(
            '',
            400,
            [],
        );
    }
}