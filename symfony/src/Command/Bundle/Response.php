<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\MediaType;
use App\Command\OpenApi\Operation;

class Response
{
    private readonly Controller $controller;
    private readonly array $responsex;

    /**
     * @param array<Operation> $operations
     */
    public static function build(Operation $operation, Response $response, MediaType $mediaType): self
    {
    }

    private function __construct()
    {
    }
}