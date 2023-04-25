<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class LogoutUser100EmptyResponse extends Response
{
    public function __construct(
    ) {
        parent::__construct(
            '',
            100,
            [],
        );
    }
}