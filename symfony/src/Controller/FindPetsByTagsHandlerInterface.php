<?php

namespace App\Controller;

interface FindPetsByTagsHandlerInterface
{
    /**
     * OperationId: findPetsByTags
     */
    public function handle(
        ?string $qTags = ''
    ): ;
}
