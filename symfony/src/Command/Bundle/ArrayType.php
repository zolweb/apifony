<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class ArrayType implements Type
{
    private readonly Schema $schema;
    private readonly Type $itemType;

    /**
     * @throws Exception
     */
    public function __construct(
        Reference|Schema $schema,
        string $className,
        Components $components,
    ) {
        if ($schema instanceof Reference) {
            $schema = $components->schemas[$schema->getName()];
        }

        $items = $schema->items;
        if ($items instanceof Reference) {
            $items = $components->schemas[$className = $items->getName()];
            $className = u($className)->camel()->title();
        }

        $this->schema = $schema;
        $this->itemType = match ($items->type) {
            'string' => new StringType($items),
            'integer' => new IntegerType($items),
            'number' => new NumberType($items),
            'boolean' => new BooleanType($items),
            'object' => new ObjectType($items, $className, $components),
            'array' => throw new Exception('Arrays of array are not supported.'),
        };
    }

    public function getMethodParameterType(): string
    {
        return 'array';
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return "array<{$this->itemType->getPhpDocParameterAnnotationType()}>";
    }

    public function getMethodParameterDefault(): ?string
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Array path parameters are not supported.');
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        throw new Exception('Array parameters are not supported.');
    }

    public function getRequestBodyPayloadInitializationFromRequest(): string
    {
        return $this->schema->type === 'object' ?
            "\$requestBodyPayload = \$this->serializer->deserialize(\$request->getContent(), 'Flex[]', JsonEncoder::FORMAT);" :
            '$requestBodyPayload = json_decode($request->getContent(), true)';
    }

    public function getRequestBodyPayloadValidationViolationsInitialization(): string
    {
        return $this->schema->type === 'object' ?
            '$violations = $this->validator->validate($requestBodyPayload, [new Assert\Valid()]);' :
            sprintf(
                "\$violations = \$this->validator->validate(\$requestBodyPayload, [\n%s\n]);",
                implode(
                    '',
                    array_map(
                        static fn (Constraint $constraint) => $constraint->getInstantiation(5),
                        $this->getConstraints(),
                    ),
                ),
            );
    }

    public function getNormalizedType(): string
    {
        return "{$this->itemType->getNormalizedType()}Array";
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return "is_array(\$requestBodyPayload) && {$this->itemType->getRequestBodyPayloadTypeChecking()}";
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->schema->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(sprintf('Assert%s', u($this->schema->format)->camel()->title()), []);
        }

        if ($this->schema->minItems !== null) {
            $constraints[] = new Constraint('Assert\Count', ['min' => $this->schema->minItems]);
        }

        if ($this->schema->maxItems) {
            $constraints[] = new Constraint('Assert\Count', ['max' => $this->schema->maxItems]);
        }

        if ($this->schema->uniqueItems) {
            $constraints[] = new Constraint('Assert\Unique', []);
        }

        if (count($this->itemType->getConstraints()) > 0) {
            $constraints[] = new Constraint('Assert\All', ['constraints' => $this->itemType->getConstraints()]);
        }

        return $constraints;
    }
}