<?php

namespace App\Controller;

interface FindPetsByTagsHandlerInterface
{
    public function handleNullApplicationJson(
            string $qTags,
    );
}
