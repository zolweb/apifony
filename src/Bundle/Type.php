<?php

namespace Zol\Ogen\Bundle;

interface Type
{
    public function isNullable(): bool;

    public function getPhpDocParameterAnnotationType(): string;

    public function getMethodParameterType(): string;

    public function getMethodParameterDefault(): ?string;

    public function getRouteRequirementPattern(): string;

    public function getStringToTypeCastFunction(): string;

    public function getNormalizedType(): string;

    public function getRequestBodyPayloadTypeChecking(): string;

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array;

    public function getBuiltInPhpType(): string;

    public function getInitValue(): string;

    public function getUsedModel(): ?string;
}