<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class FindPetsByTags400EmptyResponse extends Response
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