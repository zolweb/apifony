<?php

namespace Zol\TestOpenApiServer\Api;

use Exception;

class DenormalizationException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}