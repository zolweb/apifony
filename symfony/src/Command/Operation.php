<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Operation
{
    public readonly PathItem $pathItem;
    public readonly string $method;
    public readonly string $operationId;
    public readonly int $priority;
    /** @var array<Parameter> */
    public readonly array $parameters;
    public readonly ?RequestBody $requestBody;
    public readonly ?Responses $responses;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(PathItem $pathItem, string $method, array& $components, array $data): self
    {
        $operation = new self();
        $operation->method = $method;
        $operation->pathItem = $pathItem;
        $operation->operationId = $data['operationId'];
        $operation->priority = $data['x-priority'] ?? 0;
        $operation->parameters = array_map(
            fn (array $parameterData) => Parameter::build(
                u($data['operationId'])->camel()->title(),
                $components,
                $parameterData,
            ),
            $data['parameters'] ?? []
        );
        $operation->requestBody = isset($data['requestBody']) ?
            RequestBody::build(
                u($data['operationId'])->camel()->title(),
                $components,
                $data['requestBody'],
            ) : null;
        $operation->responses = isset($data['responses']) ?
            Responses::build(
                u($data['operationId'])->camel()->title(),
                $components,
                $data['responses'],
            ) : null;

        return $operation;
    }

    private function __construct()
    {
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
        return u($this->operationId)->camel()->title();
    }

    public function getRequestBodyContentTypes(): array
    {
        $requestBodyTypes = [];

        if ($this->requestBody === null || !$this->requestBody->required) {
            $requestBodyTypes['null'] = [
                'contentTypeChecking' => 'is_null($content)',
            ];
        }

        foreach ($this->requestBody?->mediaTypes ?? [] as $mediaType) {
            $requestBodyTypes[$mediaType->schema->type->getNormalizedType()] = [
                'contentTypeChecking' => $mediaType->schema->type->getContentTypeChecking(),
            ];
        }

        return $requestBodyTypes;
    }

    public function getResponseBodyContentTypes(): array
    {
        $responseBodyTypes = [];

        foreach ($this->responses->responses as $response) {
            foreach ($response->mediaTypes as $mediaType) {
                $responseBodyTypes[] = $mediaType->schema->type->getNormalizedType();
            }
        }

        return $responseBodyTypes;
    }

    /**
     * @return array<Parameter>
     */
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
                ((int)($param1->schema->default !== null) - (int)($param2->schema->default !== null)) ?:
                    strcmp($param1->name, $param2->name),
        );

        return $params;
    }

    public function addFiles(array& $files): void
    {
        if (!isset($files[$this->getControllerClassName()])) {
            $files[$this->getControllerClassName()] = ['template' => 'controller.php.twig', 'params' => ['operation' => $this]];
            $files[$this->getHandlerInterfaceName()] = ['template' => 'handler.php.twig', 'params' => ['operation' => $this]];

            foreach ($this->parameters as $parameter) {
                $parameter->addFiles($files);
            }

            $this->requestBody?->addFiles($files);
            $this->responses?->addFiles($files);
        }
    }
}