<?php

namespace App\Command\Bundle;

interface File
{
    public function getFolder(): string;

    public function getName(): string;

    public function getTemplate(): string;

    public function getParametersRootName(): string;
}