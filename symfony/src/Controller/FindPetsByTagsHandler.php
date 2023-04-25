<?php

namespace App\Controller;

interface FindPetsByTagsHandler
{
    public function handle(
        ?array $tags,
    ): void
}}
