<?php

namespace App\Controller;

interface FindPetsByTagsHandlerInterface
{
    public function handle(
        string $qTags,
    ) ;
}
