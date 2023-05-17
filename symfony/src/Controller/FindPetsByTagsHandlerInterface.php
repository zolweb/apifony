<?php

namespace App\Controller;

interface FindPetsByTagsHandlerInterface
{
    public function handleEmptyApplicationJson(
        string $qTags,
    ):
        FindPetsByTags200PetArray;
}
