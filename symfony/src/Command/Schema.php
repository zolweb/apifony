<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Schema
{
    private readonly SchemaType $type;
    private readonly ?string $format;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly MediaType|Parameter|Schema|Header $context,
        private readonly ?string $name,
        private readonly bool $required,
        array $data,
    ) {
        $schemaName = null;

        if (isset($data['$ref'])) {
            ['name' => $schemaName, 'data' => $data] = $context->resolveReference($data['$ref']);
        }

        $this->type = match ($data['type'] ?? null) {
            'string' => new StringSchema($name, $data),
            'integer' => new IntegerSchema($name, $data),
            'number' => new NumberSchema($name, $data),
            'array' => new ArraySchema($context, $data),
            'object' => new ObjectSchema($context, $schemaName, $name, $data),
            'boolean' => new BooleanSchema($name, $data),
            default => throw new Exception('Schemas without type are not supported.'),
        };

        $this->format = $data['format'] ?? null;
    }

    public function resolveReference(string $reference): array
    {
        return $this->context->resolveReference($reference);
    }

    public function getFormatDefinitionInterfaceName(): string
    {
        return "{$this->getNormalizedFormat()}Definition";
    }

    public function getFormatConstraintClassName(): string
    {
        return $this->getNormalizedFormat();
    }

    public function getFormatValidatorClassName(): string
    {
        return "{$this->getNormalizedFormat()}Validator";
    }

    public function getNormalizedFormat(): string
    {
        return u($this->format)->camel()->title();
    }

    public function getPhpDocParameterAnnotation(): string
    {
        return sprintf(
            '@param %s%s $%s',
            $this->required ? '' : '?',
            $this->getPhpDocParameterAnnotationType(),
            $this->name,
        );
    }

    public function getMethodParameter(): string
    {
        return sprintf(
            '%s%s $%s%s',
            $this->required ? '' : '?',
            $this->getMethodParameterType(),
            $this->name,
            $this->getMethodParameterDefault() !== null ? sprintf(
                ' = %s',
                $this->getMethodParameterDefault(),
            ) : '',
        );
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return $this->type->getPhpDocParameterAnnotationType();
    }

    public function getMethodParameterType(): string
    {
        return $this->type->getMethodParameterType();
    }

    public function getMethodParameterDefault(): ?string
    {
        return $this->type->getMethodParameterDefault();
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirement(): string
    {
        return $this->type->getRouteRequirement();
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        return $this->type->getStringToTypeCastFunction();
    }

    public function getContentInitializationFromRequest(): string
    {
        return $this->type->getContentInitializationFromRequest();
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return $this->type->getContentValidationViolationsInitialization();
    }

    public function getNormalizedType(): string
    {
        return $this->type->getNormalizedType();
    }

    public function getContentTypeChecking(): string
    {
        return $this->type->getContentTypeChecking();
    }

    public function getConstraints(): array
    {
        $constraints = $this->type->getConstraints();

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->format !== null) {
            $constraints[] = new Constraint($this->getNormalizedFormat(), []);
        }

        return $constraints;
    }

    public function getFiles(): array
    {
        return array_merge(
            $this->type->getFiles(),
            $this->format !== null ?
                [
                    $this->getFormatDefinitionInterfaceName() => ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]],
                    $this->getFormatConstraintClassName() => ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]],
                    $this->getFormatValidatorClassName() => ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]],
                ] : [],
        );
    }
}