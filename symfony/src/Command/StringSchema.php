<?php

namespace App\Command;

class StringSchema implements SchemaType
{
    private readonly ?string $default;
    private readonly ?string $pattern;
    private readonly ?int $minLength;
    private readonly ?int $maxLength;
    private readonly ?array $enum;

    public function __construct(private readonly ?string $name, array $data)
    {
        $this->default = $data['default'] ?? null;
        $this->pattern = $data['pattern'] ?? null;
        $this->minLength = $data['minLength'] ?? null;
        $this->maxLength = $data['maxLength'] ?? null;
        $this->enum = $data['enum'] ?? null;
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'string';
    }

    public function getMethodParameterType(): string
    {
        return 'string';
    }

    public function getMethodParameterDefault(): ?string
    {
        return sprintf('\'%s\'', str_replace('\'', '\\\'', $this->default));
    }

    public function getRouteRequirement(): string
    {
        return sprintf(
            '\'%s\' => \'%s\',',
            $this->name,
            $this->pattern !== null ?
                str_replace('\'', '\\\'', $this->pattern)
                : '[^:/?#[]@!$&\\\'()*+,;=]+',
        );
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'strval';
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
        return 'String';
    }

    public function getContentTypeChecking(): string
    {
        return 'is_string($content)';
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->pattern !== null) {
            $constraints[] = new Constraint('Assert\Regex', ['pattern' => $this->pattern]);
        }

        if ($this->minLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['min' => $this->minLength]);
        }

        if ($this->maxLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['max' => $this->maxLength]);
        }

        if ($this->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->enum]);
        }

        return $constraints;
    }

    public function getFiles(): array
    {
        return [];
    }
}