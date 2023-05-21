<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractController
{
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly ValidatorInterface $validator,
    ) {
    }
}
