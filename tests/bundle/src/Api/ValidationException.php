<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

class ValidationException extends \Exception
{
    /**
     * @param list<array{path: string, code: string, message: string}> $errors
     */
    public function __construct(public readonly array $errors)
    {
        parent::__construct();
    }
}