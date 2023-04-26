<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DeleteUser404EmptyResponse extends Response
{
    public function __construct(
    ) {
        parent::__construct(
            '',
            404,
            [],
        );
    }
}