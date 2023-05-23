<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\MediaType;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class ActionRequestBody
{
    /**
     * @throws Exception
     */
    public static function build(
        string $actionName,
        ?string $mimeType,
        ?MediaType $mediaType,
        Components $components,
    ): self {
        $className = u(sprintf('%s_%s_RequestBodyPayload', $actionName, $mimeType ?? 'empty'))->camel()->title();

        $payloadType = null;
        if ($mediaType !== null) {
            if ($mediaType->schema === null) {
                throw new Exception('Mediatypes without schema are not supported.');
            }
            $schema = $mediaType->schema;
            if ($schema instanceof Reference) {
                $schema= $components->schemas[$schema->getName()];
            }
            $payloadType = match ($schema->type) {
                'string' => new StringType($schema),
                'integer' => new IntegerType($schema),
                'number' => new NumberType($schema),
                'boolean' => new BooleanType($schema),
                'object' => new ObjectType($schema, $className, $components),
                'array' => new ArrayType($schema, $className, $components),
            };
        }

        return new self(
            $mimeType,
            $payloadType,
        );
    }

    private function __construct(
        private readonly ?string $mimeType,
        private readonly ?Type $payloadType,
    ) {
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getPayloadNormalizedType(): string
    {
        return $this->payloadType?->getNormalizedType() ?? 'Empty';
    }

    public function getPayloadPhpType(): string
    {
        return $this->payloadType->getPhpDocParameterAnnotationType();
    }

    public function initializationFromRequest(): string
    {
        return $this->payloadType->getRequestBodyPayloadInitializationFromRequest();
    }

    public function validationViolationsInitialization(): string
    {
        return $this->payloadType->getRequestBodyPayloadValidationViolationsInitialization();
    }
}