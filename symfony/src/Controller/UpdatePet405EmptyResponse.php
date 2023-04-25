<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class UpdatePet405EmptyResponse extends Response
{
    public function __construct(
    ) {
        parent::__construct(
            '',
            405,
            [],
        );
    }
}