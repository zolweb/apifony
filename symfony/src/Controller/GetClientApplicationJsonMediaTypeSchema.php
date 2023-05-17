<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetClientApplicationJsonMediaTypeSchema
{
    public function __construct(
        public readonly string $id,
    ) {
    }
%}
