<?php

namespace App\Command;

interface Node
{
    public function resolveReference(string $reference): array;
}