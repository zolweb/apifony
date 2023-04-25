<?php

namespace App\Controller;

interface FindPetsByStatusHandler
{
    public function handle(
        ?string $status,
    ): FindPetsByStatus200ApplicationJsonResponse|FindPetsByStatus400EmptyResponse;
}
