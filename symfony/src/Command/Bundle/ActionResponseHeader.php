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
        ?Components $components,
    ): self {
        if ($header instanceof Reference) {
            if ($components === null || !isset($components->headers[$header->getName()])) {
                throw new Exception('Reference not found in headers components.');
            }
            $header = $components->headers[$header->getName()];
        }
        $schema = $header->schema;
        if ($schema === null) {
            throw new Exception('Header objets without schema attribute are not supported.');
        }
        if ($schema instanceof Reference) {
            if ($components === null || !isset($components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.');
            }
            $schema = $components->schemas[$schema->getName()];
        }
        $type = TypeFactory::build('', $schema, $components);
        if ($type instanceof ObjectType) {
            throw new Exception('Headers of object type are not supported.');
        }
        if ($type instanceof ArrayType) {
            throw new Exception('Headers of array type are not supported.');
        }

        return new self($name, $type);
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