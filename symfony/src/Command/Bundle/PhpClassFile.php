<?php

namespace App\Command\Bundle;

interface PhpClassFile extends File
{
    public function getNamespace(): string;

    public function getClassName(): string;

    /**
     * @return array<self>
     */
    public function getUsedPhpClassFiles(): array;
}