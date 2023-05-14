<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Operation
{
    private readonly array $parameters;
    public readonly string $id;
    public readonly int $priority;
    public readonly ?RequestBody $requestBody;

    public function __construct(
        private readonly Path $path,
        public readonly string $method,
        array $data,
    ) {
        $this->parameters = array_map(
            fn (array $data) => new Parameter($this, $data),
            $data['parameters'] ?? []
        );

        $this->id = $data['operationId'];
        $this->priority = $data['x-priority'] ?? 0;
        $this->requestBody = isset($data['requestBody']) ? new RequestBody($this, $data['requestBody']) : null;
    }

    public function getRoute(): string
    {
        return $this->path->route;
    }

    public function getControllerClassName(): string
    {
        return "{$this->getNormalizedName()}Controller";
    }

    public function getHandlerInterfaceName(): string
    {
        return "{$this->getNormalizedName()}HandlerInterface";
    }

    public function getNormalizedName(): string
    {
        return u($this->id)->camel()->title();
    }

    public function getRequestBodyContentTypes(): array
    {
        $requestBodyTypes = [];

        if ($this->requestBody === null || !$this->requestBody->required) {
            $requestBodyTypes['null'] = [
                'case' => 'is_null($content)',
            ];
        }

        foreach ($this->requestBody?->mediaTypes as $mediaType) {
            $requestBodyTypes[$mediaType->getNormalizedType()] = [
                'contentTypeChecking' => $mediaType->getContentTypeChecking(),
            ];
        }

        return $requestBodyTypes;
    }

    public function getAllSortedParameters(array $in = ['path', 'query', 'cookie', 'header']): array
    {
        $pathParams = array_combine(
            array_map(fn (Parameter $param) => "{$param->in}:{$param->name}", $this->path->parameters),
            $this->path->parameters,
        );

        $operationParams = array_combine(
            array_map(fn (Parameter $param) => "{$param->in}:{$param->name}", $this->parameters),
            $this->parameters,
        );

        $params = array_filter(
            array_values(array_merge($pathParams, $operationParams)),
            static fn (Parameter $param) => in_array($param->in, $in, true),
        );

        usort(
            $params,
            static fn (Parameter $param1, Parameter $param2) =>
                ((int)$param1->hasDefault() - (int)$param2->hasDefault()) ?: strcmp($param1->name, $param2->name),
        );

        return $params;
    }

    public function getFiles(): array
    {
        return array_merge(
            [
                $this->getControllerClassName() =>
                    ['template' => 'controller.php.twig', 'params' => ['operation' => $this]],
                $this->getHandlerInterfaceName() =>
                    ['template' => 'handler.php.twig', 'params' => ['operation' => $this]],
            ],
            $this->requestBody?->getFiles() ?? [],
        );
    }

    public function resolveReference(string $reference): array
    {
        return $this->path->resolveReference($reference);
    }
}