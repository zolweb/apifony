<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

interface File
{
    public function getFolder(): string;

    public function getName(): string;

    public function getContent(): string;
}
