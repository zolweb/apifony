<?php

namespace App\Command;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GenExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('ref', [$this, 'ref'])
        ];
    }

    public function ref($ref): string
    {
        [,, $type, $class] = explode('/', $ref);

        return $class . ucfirst(substr($type, 0, -1));
    }
}