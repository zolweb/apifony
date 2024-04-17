<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

class DenormalizationException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}