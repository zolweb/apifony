<?php

namespace App\Controller;

interface UpdatePetHandlerInterface
{
    public function handleUpdatePetApplicationJsonMediaTypeSchemaApplicationJson(
            UpdatePetApplicationJsonMediaTypeSchema $content,
    );
}
