<?php

namespace App\Controller;

interface FindPetsByStatusHandler
{
    public function handle(
        ?string $status,
    ): void;
}
