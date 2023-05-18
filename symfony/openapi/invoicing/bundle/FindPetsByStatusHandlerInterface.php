<?php

namespace App\Controller;

interface FindPetsByStatusHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        string $qStatus,
    ):
        FindPetsByStatus200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $qStatus,
    ):
        FindPetsByStatus400Empty;
}
