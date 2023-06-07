<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api;

use Exception;

class DenormalizationException extends Exception
{
    public function __construct(string $message)
    {
        return parent::__construct($message);
    }
}