<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class GetClient201ApplicationJsonResponse extends Response
{
    public function __construct(
        GetClient201ApplicationJsonResponsePayload $payload,
    ) {
        parent::__construct(
            '',
            201,
            [],
        );
    }
}