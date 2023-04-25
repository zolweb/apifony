<?php

namespace App\Controller;

interface FindPetsByTagsHandler
{
    public function handle(
        ?array $tags,
    ): FindPetsByTags200ApplicationJsonResponse|FindPetsByTags400EmptyResponse;
}
