<?php

namespace App\Command;

class BooleanSchema extends Schema
{
    private readonly ?bool $default;
    private readonly ?array $enum;

    public function __construct(
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required, $data['format'] ?? null);

        $this->default = $data['default'] ?? null;
        $this->enum = $data['enum'] ?? null;
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'bool';
    }

    public function getMethodParameterType(): string
    {
        return 'bool';
    }

    public function getMethodParameterDefault(): ?string
    {
        return [true => 'true', false => 'false', null => null][$this->default];
    }

    public function getRouteRequirement(): string
    {
        return "'{$this->name}' => 'true|false',";
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'boolval';
    }

    public function getContentInitializationFromRequest(): string
    {
        return '$content = json_decode($request->getContent(), true);';
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return sprintf(
            "\$violations = \$validator->validate(\$content, [\n%s\n]);",
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
        return 'Boolean';
    }

    public function getContentTypeChecking(): string
    {
        return 'is_bool($content)';
    }

    public function getConstraints(): array
    {
        $constraints = parent::getConstraints();

        if ($this->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->enum]);
        }

        return $constraints;
    }
}