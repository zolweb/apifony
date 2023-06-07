<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api;

use Exception;

class RequestBodyValidationException extends Exception
{
    /**
     * @param array<string, array<string>> $messages
     */
    public function __construct(
        public readonly array $messages,
    ) {
    }
}