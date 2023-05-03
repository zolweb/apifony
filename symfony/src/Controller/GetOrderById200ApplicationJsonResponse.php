<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetOrderById200ApplicationJsonResponse extends Response
{
    public function __construct(
        OrderSchema $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}