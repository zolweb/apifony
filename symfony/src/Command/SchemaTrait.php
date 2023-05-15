<?php

namespace App\Command;

use function Symfony\Component\String\u;

class SchemaTrait
{
    public readonly MediaType|Parameter|SchemaTrait|Header $parent;
    public readonly SchemaType $typeHelper;
    public readonly string $type;
    public readonly bool $nullable;
    public readonly ?string $format;

    /**
     * @throws Exception
     */
    public static function build(
        MediaType|Parameter|SchemaTrait|Header $parent,
        string                                 $name,
        array                                  $componentsData,
        array                                  $data,
    ): self {
        $schema = new self();

        $schemaName = null;
        if (isset($data['$ref'])) {
            [, , $type, $schemaName] = explode('/', $data['$ref']);
            $data = $componentsData[$type][$schemaName];
        }

        if (!isset($data['type'])) {
            throw new Exception('Schemas without type are not supported.');
        }

        $nullable = false;
        if ($data['type'] === 'null') {
            throw new Exception('Null schemas are not supported.');
        } if (is_array($data['type'])) {
            if (count($data['type']) > 2 || !in_array('null', $data['type'], true)) {
                throw new Exception('Schemas with multiple types (but \'null\') are not supported.');
            }
            $nullable = true;
            $type = $data['type'][(int)($data['type'][0] === 'null')];
        } else {
            $type = $data['type'];
        }

        $schema->type = $type;
        $schema->nullable = $nullable;
        $schema->format = $data['format'] ?? null;
        $schema->typeHelper = match ($type) {
            'string' => new StringSchema($name, $data),
            'integer' => new IntegerSchema($name, $data),
            'number' => new NumberSchema($name, $data),
            'array' => new ArraySchema($parent, $data),
            'object' => new ObjectSchema($parent, $schemaName, $name, $data),
            'boolean' => new BooleanSchema($name, $data),
        };

        return $schema;
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
            $this->nullable ? '' : '?',
            $this->getPhpDocParameterAnnotationType(),
            $this->name,
        );
    }

    public function getMethodParameter(): string
    {
        return sprintf(
            '%s%s $%s%s',
            $this->nullable ? '' : '?',
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
        return $this->typeHelper->getPhpDocParameterAnnotationType();
    }

    public function getMethodParameterType(): string
    {
        return $this->typeHelper->getMethodParameterType();
    }

    public function getMethodParameterDefault(): ?string
    {
        return $this->typeHelper->getMethodParameterDefault();
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirement(): string
    {
        return $this->typeHelper->getRouteRequirement();
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        return $this->typeHelper->getStringToTypeCastFunction();
    }

    public function getContentInitializationFromRequest(): string
    {
        return $this->typeHelper->getContentInitializationFromRequest();
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return $this->typeHelper->getContentValidationViolationsInitialization();
    }

    public function getNormalizedType(): string
    {
        return $this->typeHelper->getNormalizedType();
    }

    public function getContentTypeChecking(): string
    {
        return $this->typeHelper->getContentTypeChecking();
    }

    public function getConstraints(): array
    {
        $constraints = $this->typeHelper->getConstraints();

        if ($this->nullable) {
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
            $this->typeHelper->getFiles(),
            $this->format !== null ?
                [
                    $this->getFormatDefinitionInterfaceName() => ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]],
                    $this->getFormatConstraintClassName() => ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]],
                    $this->getFormatValidatorClassName() => ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]],
                ] : [],
        );
    }
}