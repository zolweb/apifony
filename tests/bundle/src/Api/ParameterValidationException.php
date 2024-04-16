<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

use Exception;

class ParameterValidationException extends Exception
{
    /**
     * @param array<string> $messages
     */
    public function __construct(
        public readonly array $messages,
    ) {
        parent::__construct();
    }
}