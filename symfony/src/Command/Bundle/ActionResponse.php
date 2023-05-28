<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Header;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Response;
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
        Response $response,
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
            $payload !== null ? TypeFactory::build($className, $payload, $components) : null,
            array_map(
                static fn (string $name) =>
                    ActionResponseHeader::build($name, $response->headers[$name], $components),
                array_keys($response->headers),
            ),
        );
    }

    /**
     * @param array<ActionResponseHeader> $headers
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly string $name,
        private readonly string $code,
        private readonly ?string $contentType,
        private readonly ?Type $payloadType,
        private readonly array $headers,
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
        return $this->payloadType?->getMethodParameterType();
    }

    /**
     * @return array<ActionResponseHeader>
     */
    public function getHeaders(): array
    {
        return $this->headers;
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