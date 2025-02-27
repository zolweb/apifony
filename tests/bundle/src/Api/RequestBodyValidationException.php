<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

class RequestBodyValidationException extends \Exception
{
    /**
     * @param array<string, string[]> $messages
     */
    public function __construct(public readonly array $messages)
    {
        parent::__construct();
    }
}
