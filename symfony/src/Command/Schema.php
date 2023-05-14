<?php

namespace App\Command;

use Exception;

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
    ) {
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

    public abstract function getPhpDocParameterAnnotationType(): string;

    public abstract function getMethodParameterType(): string;

    public abstract function getMethodParameterDefault(): ?string;

    public abstract function getRouteRequirement(): string;

    public abstract function getStringToTypeCastFunction(): string;

    public abstract function getContentInitializationFromRequest(): string;

    public abstract function getContentValidationViolationsInitialization(): string;

    public abstract function getNormalizedType(): string;

    public abstract function getContentTypeChecking(): string;

    public abstract function getConstraints(): array;

    public abstract function getFiles(): array;
}