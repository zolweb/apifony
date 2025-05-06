<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;

class TypeFactory
{
    /**
     * @throws Exception
     */
    public static function build(string $className, Reference|Schema $schema, ?Components $components): Type
    {
        if ($schema instanceof Reference) {
            if ($components === null || !isset($components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.', $schema->path);
            }
            $schema = $components->schemas[$schema->getName()];
        }

        $nullable = false;
        if (\is_array($schema->type)) {
            if (\count($schema->type) === 0) {
                throw new Exception('Schemas without type are not supported.', $schema->path);
            }
            if (\count($schema->type) === 1) {
                $type = $schema->type[0];
            } else {
                if (\count($schema->type) > 2 || !\in_array('null', $schema->type, true)) {
                    throw new Exception('Schemas with multiple types (but \'null\') are not supported.', $schema->path);
                }
                $nullable = true;
                $type = $schema->type[(int) ($schema->type[0] === 'null')];
            }
        } else {
            $type = $schema->type;
        }

        if ($type === 'null') {
            throw new Exception('Schemas with null type only are not supported.', $schema->path);
        }

        return match ($type) {
            'string' => new StringType($schema, $nullable),
            'integer' => new IntegerType($schema, $nullable),
            'number' => new NumberType($schema, $nullable),
            'boolean' => new BooleanType($schema, $nullable),
            'object' => new ObjectType($schema, $nullable, $className),
            'array' => new ArrayType($schema, $nullable, $className, $components),
            default => throw new \RuntimeException('Unexpected type.'),
        };
    }
}
