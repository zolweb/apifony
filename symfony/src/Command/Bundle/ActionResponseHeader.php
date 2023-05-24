<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Header;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class ActionResponseHeader
{
    /**
     * @throws Exception
     */
    public static function build(
        string $name,
        Reference|Header $header,
        Components $components,
    ): self {
        if ($header instanceof Reference) {
            $header = $components->parameters[$header->getName()];
        }
        $schema = $header->schema;
        if ($schema instanceof Reference) {
            $schema = $components->schemas[$schema->getName()];
        }

        return new self(
            $name,
            match ($schema->type) {
                'string' => new StringType($schema),
                'integer' => new IntegerType($schema),
                'number' => new NumberType($schema),
                'boolean' => new BooleanType($schema),
                'object' => throw new Exception('Headers of object type are not supported.'),
                'array' => throw new Exception('Headers of array type are not supported.'),
            },
        );
    }

    private function __construct(
        private readonly string $name,
        private readonly Type $type,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVariableName(): string
    {
        return u($this->name)->camel()->title();
    }

    public function getPhpType(): string
    {
        return $this->type->getMethodParameterType();
    }
}