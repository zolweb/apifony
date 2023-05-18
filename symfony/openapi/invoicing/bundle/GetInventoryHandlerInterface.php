<?php

namespace App\Controller;

interface GetInventoryHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
    ):
        GetInventory200ApplicationJson;
}
