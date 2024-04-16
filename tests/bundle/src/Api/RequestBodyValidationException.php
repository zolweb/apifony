<?php

namespace Zol\TestOpenApiServer\Api;

use Exception;

class RequestBodyValidationException extends Exception
{
    /**
     * @param array<string, array<string>> $messages
     */
    public function __construct(
        public readonly array $messages,
    ) {
        parent::__construct();
    }
}