<?php

namespace App\Command;

interface Type
{
    public function getPhpDocParameterAnnotationType(): string;

    public function getMethodParameterType(): string;

    public function getMethodParameterDefault(): ?string;

    public function getRouteRequirementPattern(): string;

    public function getStringToTypeCastFunction(): string;

    public function getRequestBodyPayloadInitializationFromRequest(): string;

    public function getRequestBodyPayloadValidationViolationsInitialization(): string;

    public function getNormalizedType(): string;

    public function getRequestBodyPayloadTypeChecking(): string;

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array;

    /**
     * @param array<string, array{template: string, params: array<string, mixed>}> $files
     */
    public function addFiles(array& $files): void;

    public function __toString(): string;
}