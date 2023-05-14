<?php

namespace App\Command;

use function Symfony\Component\String\u;

abstract class Schema
{
    /**
     * @throws Exception
     */
    public static function build(
        MediaType|Parameter|Schema|Header $context,
        ?string $name,
        bool $required,
        array $data,
    ): Schema {
        $schemaName = null;

        if (isset($data['$ref'])) {
            ['name' => $schemaName, 'data' => $data] = $context->resolveReference($data['$ref']);
        }

        return match ($data['type'] ?? null) {
            'string' => new StringSchema($name, $required, $data),
            'integer' => new IntegerSchema($name, $required, $data),
            'number' => new NumberSchema($name, $required, $data),
            'array' => new ArraySchema($context, $name, $required, $data),
            'object' => new ObjectSchema($context, $schemaName, $name, $required, $data),
            'boolean' => new BooleanSchema($name, $required, $data),
            default => throw new Exception('Schemas without type are not supported.'),
        };
    }

    public function __construct(
        protected readonly ?string $name,
        protected readonly bool $required,
        protected readonly ?string $format,
    ) {
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

    public function getConstraints(): array
    {
        $constraints = [];

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
        return $this->format !== null ?
            [
                $this->getFormatDefinitionInterfaceName() => ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]],
                $this->getFormatConstraintClassName() => ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]],
                $this->getFormatValidatorClassName() => ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]],
            ] : [];
    }

    public abstract function getPhpDocParameterAnnotationType(): string;

    public abstract function getMethodParameterType(): string;

    public abstract function getMethodParameterDefault(): ?string;

    public abstract function getRouteRequirement(): string;

    public abstract function getStringToTypeCastFunction(): string;

    public abstract function getContentInitializationFromRequest(): string;

    public abstract function getContentValidationViolationsInitialization(): string;

    public abstract function getNormalizedType(): string;

    public abstract function getContentTypeChecking(): string;
}