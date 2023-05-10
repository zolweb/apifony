<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteOrder200EmptyResponse extends Response
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