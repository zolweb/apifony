<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Schema;

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

        // x-apifony-raw means "any value": the declared type, if there is one at all, is ignored.
        // Deciding it here is what lets a raw schema omit its type attribute, which is how
        // OpenAPI 3.1 already spells "any type".
        if (($schema->extensions['x-apifony-raw'] ?? false) === true) {
            return new RawType($schema);
        }

        $type = null;
        $nullable = false;
        if ($schema->type === null) {
            if (\count($schema->enum) === 0) {
                throw new Exception('Schemas without type nor enum elements are not supported.', $schema->path);
            }
            foreach ($schema->enum as $e) {
                switch (true) {
                    case \is_string($e):
                        if ($type !== null && $type !== 'null' && $type !== 'string') {
                            throw new Exception('Schemas with multiple types (but \'null\') are not supported.', $schema->path);
                        }
                        $type = 'string';
                        break;
                    case \is_int($e):
                        if ($type !== null && $type !== 'null' && $type !== 'integer' && $type !== 'number') {
                            throw new Exception('Schemas with multiple types (but \'null\') are not supported.', $schema->path);
                        }
                        if ($type !== 'number') {
                            $type = 'integer';
                        }
                        break;
                    case \is_float($e):
                        if ($type !== null && $type !== 'null' && $type !== 'integer' && $type !== 'number') {
                            throw new Exception('Schemas with multiple types (but \'null\') are not supported.', $schema->path);
                        }
                        $type = 'number';
                        break;
                    case \is_bool($e):
                        if ($type !== null && $type !== 'null' && $type !== 'boolean') {
                            throw new Exception('Schemas with multiple types (but \'null\') are not supported.', $schema->path);
                        }
                        $type = 'boolean';
                        break;
                    default:
                        if ($type === null) {
                            $type = 'null';
                        }
                        $nullable = true;
                        break;
                }
            }
        } elseif (\is_array($schema->type)) {
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
            'object' => new ObjectType($schema, $nullable, $className, $components),
            'array' => new ArrayType($schema, $nullable, $className, $components),
        };
    }
}
