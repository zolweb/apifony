<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

class RequestBodyValidationException extends \Exception
{
    /**
     * @param array<string, array<string>> $messages
     */
    public function __construct(public readonly array $messages)
    {
        parent::__construct();
    }
}