<?php

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Response;
use Zol\Ogen\OpenApi\Schema;
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
        ?Components $components,
    ): self {
        if (!in_array($contentType, [null, 'application/json'], true)) {
            throw new Exception('Responses with content type other thant \'application/json\' are not supported.');
        }

        $className = u(sprintf('%s_%s_%s_ResponsePayload', $actionName, $code, $contentType ?? 'empty'))->camel()->title();

        $payloadModels = [];
        $payloadType = null;
        $usedModelName = null;
        if ($payload !== null) {
            $schema = $payload;
            $hasModel = true;
            if ($schema instanceof Reference) {
                if ($components === null || !isset($components->schemas[$schema->getName()])) {
                    throw new Exception('Reference not found in schemas components.');
                }
                $schema = $components->schemas[$className = $usedModelName = $schema->getName()];
                $hasModel = false;
            }
            $payloadType = TypeFactory::build($className, $schema, $components);
            if ($payloadType instanceof ArrayType) {
                throw new Exception('Responses with array schema are not supported.');
            }

            if ($hasModel) {
                $addModels = function(string $rawName, Reference|Schema $schema) use (&$addModels, &$payloadModels, $bundleNamespace, $aggregateName, $className, $components) {
                    if (!$schema instanceof Reference) {
                        $type = TypeFactory::build('', $schema, $components);
                        if ($type instanceof ObjectType) {
                            if (!($schema->extensions['x-raw'] ?? false)) {
                                $payloadModels[$rawName] = Model::build(
                                    $bundleNamespace,
                                    "{$bundleNamespace}\Api\\{$aggregateName}",
                                    "src/Api/{$aggregateName}",
                                    $className,
                                    $schema,
                                    $components,
                                    false,
                                );
                                foreach ($schema->properties as $propertyName => $property) {
                                    $addModels("{$rawName}_{$propertyName}", $property);
                                }
                            }
                        } elseif ($type instanceof ArrayType) {
                            if ($schema->items === null) {
                                throw new Exception('Schema objects of array type without items attribute are not supported.');
                            }
                            $addModels($rawName, $schema->items);
                        }
                    }
                };

                $addModels($className, $schema);
            }
        }

        return new self(
            $bundleNamespace,
            $aggregateName,
            u(sprintf('%s_%s_%s_Response', $actionName, $code, $contentType ?? 'Empty'))->camel()->title(),
            $code,
            $contentType,
            $payloadType,
            array_map(
                static fn (string $name) =>
                    ActionResponseHeader::build($name, $response->headers[$name], $components),
                array_keys($response->headers),
            ),
            $payloadModels,
            $usedModelName,
        );
    }

    /**
     * @param array<ActionResponseHeader> $headers
     * @param array<Model> $payloadModels
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly string $name,
        private readonly string $code,
        private readonly ?string $contentType,
        private readonly ?Type $payloadType,
        private readonly array $headers,
        private readonly array $payloadModels,
        private readonly ?string $usedModelName,
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

    public function getBundleNamespace(): string
    {
        return $this->bundleNamespace;
    }

    /**
     * @return array<Model>
     */
    public function getPayloadModels(): array
    {
        return $this->payloadModels;
    }

    public function getUsedModelName(): ?string
    {
        return $this->usedModelName;
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