<?php

namespace App\Command;

interface SchemaType
{
    public function getPhpDocParameterAnnotationType(): string;

    public function getMethodParameterType(): string;

    public function getMethodParameterDefault(): ?string;

    public function getRouteRequirement(): string;

    public function getStringToTypeCastFunction(): string;

    public function getContentInitializationFromRequest(): string;

    public function getContentValidationViolationsInitialization(): string;

    public function getNormalizedType(): string;

    public function getContentTypeChecking(): string;

    public function getConstraints(): array;

    public function getFiles(): array;
}