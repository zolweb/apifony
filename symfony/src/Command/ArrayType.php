<?php

namespace App\Command;

class ArrayType implements Type
{
    public function __construct(
        private readonly Schema $schema,
    ) {
    }

    public function getMethodParameterType(): string
    {
        return 'array';
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return "array<{$this->schema->items->type->getPhpDocParameterAnnotationType()}>";
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
        return (string)$this->schema->items->type === 'object' ?
            "\$requestBodyPayload = \$serializer->deserialize(\$request->getContent(), '{$this->schema->items->className}[]', JsonEncoder::FORMAT);" :
            '$requestBodyPayload = json_decode($request->getContent(), true)';
    }

    public function getRequestBodyPayloadValidationViolationsInitialization(): string
    {
        return (string)$this->schema->items->type === 'object' ?
            '$violations = $validator->validate($requestBodyPayload, [new Assert\Valid()]);' :
            sprintf(
                "\$violations = \$validator->validate(\$requestBodyPayload, [\n%s\n]);",
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
        return "{$this->schema->items->type->getNormalizedType()}Array";
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return "is_array(\$requestBodyPayload) && {$this->schema->items->type->getRequestBodyPayloadTypeChecking()}";
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->schema->minItems !== null) {
            $constraints[] = new Constraint('Assert\Count', ['min' => $this->schema->minItems]);
        }

        if ($this->schema->maxItems) {
            $constraints[] = new Constraint('Assert\Count', ['max' => $this->schema->maxItems]);
        }

        if ($this->schema->uniqueItems) {
            $constraints[] = new Constraint('Assert\Unique', []);
        }

        if (count($this->schema->items->getConstraints()) > 0) {
            $constraints[] = new Constraint('Assert\All', ['constraints' => $this->schema->items->getConstraints()]);
        }

        return $constraints;
    }

    public function addFiles(array& $files): void
    {
        $this->schema->items->addFiles($files);
    }

    public function __toString(): string
    {
        return 'array';
    }
}