<?php

namespace App\Command;

interface Node
{
    public function getFiles(): array;
}