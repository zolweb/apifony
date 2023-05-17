<?php

namespace App\Controller;

interface FindPetsByTagsHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        string $qTags,
    ):
        FindPetsByTags200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $qTags,
    ):
        FindPetsByTags400Empty;
}
