<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetOrderById400EmptyResponse extends Response
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