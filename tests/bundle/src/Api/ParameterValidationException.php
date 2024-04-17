<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

class ParameterValidationException extends \Exception
{
    /**
     * @param string[] $messages
     */
    public function __construct(public readonly array $messages)
    {
        parent::__construct();
    }
}