<?php

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Schema;
use function Symfony\Component\String\u;

class ObjectType implements Type
{
    private readonly bool $isRaw;

    public function __construct(
        private readonly Schema $schema,
        private readonly bool $nullable,
        private readonly string $name,
    ) {
        $this->isRaw = ($this->schema->extensions['x-raw'] ?? false) === true;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return $this->isRaw ? 'array<mixed>|\stdClass' : $this->name;
    }

    public function getMethodParameterType(): string
    {
        return $this->isRaw ? 'array|\stdClass' : $this->name;
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
        throw new Exception('Object path parameters are not supported.');
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        throw new Exception('Object parameters are not supported.');
    }

    public function getNormalizedType(): string
    {
        return $this->isRaw ? 'array<mixed>|\stdClass' : $this->name;
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return $this->isRaw ? 'is_array($requestBodyPayload)' : "\$requestBodyPayload instanceOf {$this->name}";
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->isRaw) {
            $constraints[] = new Constraint('Assert\Valid', []);
        }

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return $this->isRaw ? 'array|\stdClass' : 'object';
    }

    public function getInitValue(): string
    {
        throw new \RuntimeException('Can not init object');
    }

    public function getUsedModel(): ?string
    {
        return null;
    }
}