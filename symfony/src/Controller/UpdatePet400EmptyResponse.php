<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class UpdatePet400EmptyResponse extends Response
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