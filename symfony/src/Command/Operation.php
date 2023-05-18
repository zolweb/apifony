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
    /** @var array<string> */
    public readonly array $tags;

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
        $operation->tags = $data['tags'] ?? [];

        return $operation;
    }

    private function __construct()
    {
    }

    public function getAllPossibleRequestBodyPayloadTypes(): array
    {
        $requestBodyPayloadTypes = [];

        if ($this->requestBody === null || !$this->requestBody->required) {
             $requestBodyPayloadTypes['Empty'] = null;
        }

        foreach ($this->requestBody?->mediaTypes ?? [] as $mediaType) {
            $requestBodyPayloadTypes[$mediaType->schema->type->getNormalizedType()] = $mediaType->schema->type;
        }

        return $requestBodyPayloadTypes;
    }

    public function getAllPossibleResponseContentTypes(): array
    {
        $responseContentTypes = [];

        foreach ($this->responses->responses as $response) {
            if (count($response->content) === 0) {
                $responseContentTypes['Empty'] = null;
            }

            foreach ($response->content as $mediaType) {
                $responseContentTypes[$mediaType->getNormalizedType()] = $mediaType->type;
            }
        }

        return $responseContentTypes;
    }

    public function getAllPossibleResponsesForContentType(?string $contentType): array
    {
        $responses = [];

        foreach ($this->responses->responses as $response) {
            if ($contentType === null && count($response->content) === 0) {
                $responses[] = "{$response->className}Empty";
            }

            foreach ($response->content as $mediaType) {
                if ($contentType === $mediaType->type) {
                    $responses[] = $mediaType->className;
                }
            }
        }

        return $responses;
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
        $baseName = isset($this->tags[0]) ? u($this->tags[0])->camel()->title() : 'Default';
        $folder = "Controller/{$baseName}";
        $controllerClassName = "{$baseName}Controller";
        $handlerInterfaceName = "{$baseName}HandlerInterface";

        if (!isset($files["{$folder}/{$controllerClassName}"])) {
            $files["{$folder}/{$controllerClassName}"] = [
                'folder' => $folder,
                'name' => $controllerClassName,
                'template' => 'controller.php.twig',
                'params' => [
                    'className' => $controllerClassName,
                    'interfaceName' => $handlerInterfaceName,
                    'operations' => [$this],
                ],
            ];
            $files["{$folder}/{$handlerInterfaceName}"] = [
                'folder' => $folder,
                'name' => $handlerInterfaceName,
                'template' => 'handler.php.twig',
                'params' => [
                    'interfaceName' => $handlerInterfaceName,
                    'operations' => [$this],
                ],
            ];

            $this->requestBody?->addFiles($files, $folder);
            $this->responses?->addFiles($files, $folder);
        } else {
            $files["{$folder}/{$controllerClassName}"]['params']['operations'][] = $this;
            $files["{$folder}/{$handlerInterfaceName}"]['params']['operations'][] = $this;
        }
    }
}