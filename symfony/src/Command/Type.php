<?php

namespace App\Command;

interface Type
{
    public function getPhpDocParameterAnnotationType(Schema $schema): string;

    public function getMethodParameterType(Schema $schema): string;

    public function getMethodParameterDefault(Schema $schema): ?string;

    public function getRouteRequirementPattern(Schema $schema): string;

    public function getStringToTypeCastFunction(Schema $schema): string;

    public function getContentInitializationFromRequest(Schema $schema): string;

    public function getContentValidationViolationsInitialization(Schema $schema): string;

    public function getNormalizedType(Schema $schema): string;

    public function getContentTypeChecking(Schema $schema): string;

    public function getConstraints(Schema $schema): array;

    public function getFiles(Schema $schema): array;

    public function __toString(): string;
}