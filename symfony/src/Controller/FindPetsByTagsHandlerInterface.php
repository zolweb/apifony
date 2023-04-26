<?php

namespace App\Controller;

interface FindPetsByTagsHandlerInterface
{
    /**
     * OperationId: findPetsByTags
     *
     * Finds Pets by tags
     *
     * Multiple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.
     */
    public function handle(
        ?array $tags,
    ): FindPetsByTags200ApplicationJsonResponse|FindPetsByTags400EmptyResponse;
}
