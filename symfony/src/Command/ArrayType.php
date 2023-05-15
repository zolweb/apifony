<?php

namespace App\Command;

class ArrayType implements Type
{
    public function getMethodParameterType(Schema $schema): string
    {
        return 'array';
    }

    public function getPhpDocParameterAnnotationType(Schema $schema): string
    {
        return "array<{$schema->items->getPhpDocParameterAnnotationType()}>";
    }

    public function getMethodParameterDefault(Schema $schema): ?string
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(Schema $schema): string
    {
        throw new Exception('Array path parameters are not supported.');
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(Schema $schema): string
    {
        throw new Exception('Array parameters are not supported.');
    }

    public function getContentInitializationFromRequest(Schema $schema): string
    {
        return (string)$schema->items->type === 'object' ?
            "\$content = \$serializer->deserialize(\$request->getContent(), '{$schema->items->getClassName()}[]', JsonEncoder::FORMAT);" :
            '$content = json_decode($request->getContent(), true)';
    }

    public function getContentValidationViolationsInitialization(Schema $schema): string
    {
        return (string)$schema->items->type === 'object' ?
            '$violations = $validator->validate($content, [new Assert\Valid()]);' :
            sprintf(
                "\$violations = \$validator->validate(\$content, [\n%s\n]);",
                implode(
                    '',
                    array_map(
                        static fn (Constraint $constraint) => $constraint->getInstantiation(5),
                        $this->getConstraints($schema),
                    ),
                ),
            );
    }

    public function getNormalizedType(Schema $schema): string
    {
        return "{$schema->items->getNormalizedType()}Array";
    }

    public function getContentTypeChecking(Schema $schema): string
    {
        return "is_array(\$content) && {$schema->items->getContentTypeChecking()}";
    }

    public function getConstraints(Schema $schema): array
    {
        $constraints = [];

        if ($schema->minItems !== null) {
            $constraints[] = new Constraint('Assert\Count', ['min' => $schema->minItems]);
        }

        if ($schema->maxItems) {
            $constraints[] = new Constraint('Assert\Count', ['max' => $schema->maxItems]);
        }

        if ($schema->uniqueItems) {
            $constraints[] = new Constraint('Assert\Unique', []);
        }

        if (count($schema->items->getConstraints()) > 0) {
            $constraints[] = new Constraint('Assert\All', ['constraints' => $schema->items->getConstraints()]);
        }

        return $constraints;
    }

    public function getFiles(Schema $schema): array
    {
        return $schema->items->getFiles();
    }

    public function __toString(): string
    {
        return 'array';
    }
}