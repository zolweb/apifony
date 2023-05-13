<?php

namespace App\Command;

use Exception;

class ArraySchema extends Schema
{
    private readonly Schema $items;
    private readonly ?int $minItems;
    private readonly ?int $maxItems;
    private readonly bool $uniqueItems;

    public function __construct(
        private readonly MediaType|Parameter|ObjectSchema|ArraySchema $context,
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);
        $this->minItems = $data['minItems'] ?? null;
        $this->maxItems = $data['maxItems'] ?? null;
        $this->uniqueItems = $data['uniqueItems'] ?? false;
        $this->items = Schema::build($this, null, false, $data['items']);
    }

    public function resolveReference(string $reference): array
    {
        return $this->context->resolveReference($reference);
    }

    public function getClassName(): string
    {
        return $this->schemaName ?? sprintf('%s%s', $this->context->getClassName(), ucfirst($this->name ?? ''));
    }

    public function getMethodParameterType(): string
    {
        return 'array';
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return "array<{$this->items->getPhpDocParameterAnnotationType()}>";
    }

    public function getMethodParameterDefault(): ?string
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirement(): string
    {
        throw new Exception('Array parameters in path are not supported.');
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->minItems !== null) {
            $constraints[] = new Constraint('Assert\Count', ['min' => $this->minItems]);
        }

        if ($this->maxItems) {
            $constraints[] = new Constraint('Assert\Count', ['max' => $this->maxItems]);
        }

        if ($this->uniqueItems) {
            $constraints[] = new Constraint('Assert\Unique', []);
        }

        if (count($this->items->getConstraints()) > 0) {
            $constraints[] = new Constraint('Assert\All', ['constraints' => $this->items->getConstraints()]);
        }

        return $constraints;
    }

    public function getFiles(): array
    {
        return $this->items->getFiles();
    }
}