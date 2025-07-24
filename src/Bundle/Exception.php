<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

class Exception extends \Exception
{
    /**
     * @param list<string> $path
     */
    public function __construct(string $message, public readonly array $path)
    {
        parent::__construct($message);
    }
}
