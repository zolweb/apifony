<?php

namespace Zol\TestOpenApiServer\Format;

interface MediaFolderIdDefinition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}