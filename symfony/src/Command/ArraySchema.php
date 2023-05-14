<?php

namespace App\Command;

class ArraySchema implements SchemaType
{
    private readonly Schema $items;
    private readonly ?int $minItems;
    private readonly ?int $maxItems;
    private readonly bool $uniqueItems;

    /**
     * @throws Exception
     */
    public function __construct(MediaType|Parameter|Schema|Header $context, array $data)
    {
        if (($data['items']['type'] ?? null) === 'array') {
            throw new Exception('Array schemas of arrays are not supported.');
        }

        $this->minItems = $data['minItems'] ?? null;
        $this->maxItems = $data['maxItems'] ?? null;
        $this->uniqueItems = $data['uniqueItems'] ?? false;
        $this->items = new Schema($context, null, false, $data['items']);
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

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        throw new Exception('Array parameters are not supported.');
    }

    public function getContentInitializationFromRequest(): string
    {
        return $this->items instanceof ObjectSchema ?
            "\$content = \$serializer->deserialize(\$request->getContent(), '{$this->getClassName()}[]', JsonEncoder::FORMAT);" :
            '$content = json_decode($request->getContent(), true)';
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return $this->items instanceof ObjectSchema ?
            '$violations = $validator->validate($content, [new Assert\Valid()]);' :
            sprintf(
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
        return "{$this->items->getNormalizedType()}Array";
    }

    public function getContentTypeChecking(): string
    {
        return "is_array(\$content) && {$this->items->getContentTypeChecking()}";
    }

    public function getConstraints(): array
    {
        $constraints = [];

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