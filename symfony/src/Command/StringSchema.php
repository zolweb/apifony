<?php

namespace App\Command;

use function Symfony\Component\String\u;

class StringSchema extends Schema
{
    private readonly ?string $default;
    private readonly ?string $format;
    private readonly ?string $pattern;
    private readonly ?int $minLength;
    private readonly ?int $maxLength;
    private readonly ?array $enum;

    public function __construct(
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);

        $this->default = $data['default'] ?? null;
        $this->format = $data['format'] ?? null;
        $this->pattern = $data['pattern'] ?? null;
        $this->minLength = $data['minLength'] ?? null;
        $this->maxLength = $data['maxLength'] ?? null;
        $this->enum = $data['enum'] ?? null;
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

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->format !== null) {
            $constraints[] = new Constraint($this->getNormalizedFormat(), []);
        }

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
        return $this->format !== null ?
            [
                $this->getFormatDefinitionInterfaceName() => ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]],
                $this->getFormatConstraintClassName() => ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]],
                $this->getFormatValidatorClassName() => ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]],
            ] : [];
    }
}