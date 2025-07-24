<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

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