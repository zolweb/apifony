<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class ActionResponse implements File
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        string $code,
        ?string $contentType,
        null|Reference|Schema $payload,
        Components $components,
    ): self {
        if ($payload instanceof Reference) {
            $payload = $components->schemas[$payload->getName()];
        }

        return new self(
            $bundleNamespace,
            $aggregateName,
            $className = u(sprintf('%s_%s_%s', $actionName, $code, $contentType ?? 'Empty'))->camel()->title(),
            $code,
            $contentType,
            match ($payload->type ?? 'null') {
                'string' => new StringType($payload),
                'integer' => new IntegerType($payload),
                'number' => new NumberType($payload),
                'boolean' => new BooleanType($payload),
                'object' => new ObjectType($payload, $className, $components),
                'array' => new ArrayType($payload, $className, $components),
                'null' => null,
            },
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly string $name,
        private readonly string $code,
        private readonly ?string $contentType,
        private readonly ?Type $payloadType,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function getPayloadPhpType(): ?string
    {
        return $this->payloadType->getMethodParameterType();
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return $this->name;
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->name}.php";
    }

    public function getTemplate(): string
    {
        return 'response.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'response';
    }
}