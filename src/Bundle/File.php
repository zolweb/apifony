<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

interface File
{
    public function getFolder(): string;

    public function getName(): string;

    public function getTemplate(): string;

    public function getParametersRootName(): string;

    public function getContent(): string;
}
