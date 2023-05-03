<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class UploadFile200ApplicationJsonResponse extends Response
{
    public function __construct(
        ApiResponseSchema $payload,
    ) {
        parent::__construct(
            '',
            200,
            [],
        );
    }
}