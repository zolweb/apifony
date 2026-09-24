<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Operation. Its parameter list is already the merge of the path item list
 * with the operation one, keyed on the location and name, exactly as the parsing layer computes it.
 */
final class Operation
{
    /**
     * @param list<Parameter>      $parameters
     * @param array<string, mixed> $extensions
     * @param list<string>         $path
     */
    public function __construct(
        public readonly string $operationId,
        public readonly array $parameters,
        public readonly ?RequestBody $requestBody,
        public readonly ?Responses $responses,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
