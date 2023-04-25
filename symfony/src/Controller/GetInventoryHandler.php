<?php

namespace App\Controller;

interface GetInventoryHandler
{
    public function handle(
    ): GetInventory200ApplicationJsonResponse;
}
