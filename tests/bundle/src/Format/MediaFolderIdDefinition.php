<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Format;

interface MediaFolderIdDefinition
{
    /**
     * @return string[]
     */
    public function validate(mixed $value): array;
}