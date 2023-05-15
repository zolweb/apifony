<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Operation
{
    public readonly PathItem $pathItem;
    public readonly string $method;
    public readonly string $operationId;
    public readonly int $priority;
    public readonly array $parameters;
    public readonly ?RequestBody $requestBody;
    public readonly ?Responses $responses;

    /**
     * @throws Exception
     */
    public static function build(PathItem $pathItem, string $method, array $componentsData, array $data): self
    {
        $operation = new self();
        $operation->method = $method;
        $operation->pathItem = $pathItem;
        $operation->operationId = $data['operationId'];
        $operation->priority = $data['x-priority'] ?? 0;
        $operation->parameters = array_map(
            fn (array $data) => Parameter::build($operation, $componentsData, $data),
            $data['parameters'] ?? []
        );
        $operation->requestBody = isset($data['requestBody']) ?
            RequestBody::build($operation, $componentsData, $data['requestBody']) : null;
        $operation->responses = isset($data['responses']) ?
            Responses::build($operation, $componentsData, $data['responses']) : null;

        return $operation;
    }

    private function __construct()
    {
    }

    public function getRoute(): string
    {
        return $this->pathItem->route;
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
                'contentTypeChecking' => 'is_null($content)',
            ];
        }

        foreach ($this->requestBody->mediaTypes ?? [] as $mediaType) {
            $requestBodyTypes[$mediaType->getNormalizedType()] = [
                'contentTypeChecking' => $mediaType->getContentTypeChecking(),
            ];
        }

        return $requestBodyTypes;
    }

    public function getResponseBodyContentTypes(): array
    {
        $responseBodyTypes = [];

        foreach ($this->responses as $response) {
            foreach ($response->mediaTypes as $mediaType) {
                $responseBodyTypes[] = $mediaType->getNormalizedType();
            }
        }

        return $responseBodyTypes;
    }

    public function getAllSortedParameters(array $in = ['path', 'query', 'cookie', 'header']): array
    {
        $pathItemParams = array_combine(
            array_map(fn (Parameter $param) => "{$param->in}:{$param->name}", $this->pathItem->parameters),
            $this->pathItem->parameters,
        );

        $operationParams = array_combine(
            array_map(fn (Parameter $param) => "{$param->in}:{$param->name}", $this->parameters),
            $this->parameters,
        );

        $params = array_filter(
            array_values(array_merge($pathItemParams, $operationParams)),
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
        [, , $type, $name] = explode('/', $reference);

        return [
            'type' => $type,
            'name' => $name,
            'data' => $this->componentsData[$type][$name],
        ];
    }
}