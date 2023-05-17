<?php

namespace App\Controller;

interface GetInventoryHandlerInterface
{
    public function handleEmptyApplicationJson(
    ):
        GetInventory200GetInventoryApplicationJsonMediaTypeSchema;
}
