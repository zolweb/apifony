<?php

namespace App\Command;

interface Type
{
    public function getPhpDocParameterAnnotationType(): string;

    public function getMethodParameterType(): string;

    public function getMethodParameterDefault(): ?string;

    public function getRouteRequirementPattern(): string;

    public function getStringToTypeCastFunction(): string;

    public function getContentInitializationFromRequest(): string;

    public function getContentValidationViolationsInitialization(): string;

    public function getNormalizedType(): string;

    public function getContentTypeChecking(): string;

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array;

    public function addFiles(array& $files): void;

    public function __toString(): string;
}