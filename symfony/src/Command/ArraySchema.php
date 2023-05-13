<?php

namespace App\Command;

class ArraySchema extends Schema
{
    private readonly Schema $items;
    private readonly ?int $minItems;
    private readonly ?int $maxItems;
    private readonly bool $uniqueItems;

    public function __construct(
        private readonly MediaType|Parameter|ObjectSchema|ArraySchema $context,
        private readonly ?string $name,
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

    public function getConstraints(): array
    {
        return [];
        $constraints = [];

        if ($this->minItems !== null) {
            $constraints[] = sprintf(
                'Assert\Count(min: %d)',
                $this->minItems,
            );
        }

        if ($this->maxItems) {
            $constraints[] = sprintf(
                'Assert\Count(max: %d)',
                $this->maxItems,
            );
        }

        if ($this->uniqueItems) {
            $constraints[] = 'Assert\Unique()';
        }

        $constraints[] = sprintf(
            "Assert\All([%s\n\t\t])",
            implode(
                '',
                array_map(
                    static fn (string $c) => "\n\t\t\tnew $c,",
                    $this->getConstraints($required, $schema['items'] ?? []),
                ),
            ),
        );

        return $constraints;
    }

    public function getFiles(): array
    {
        return $this->items->getFiles();
    }
}