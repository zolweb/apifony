<?php

namespace App\Controller;

interface GetInventoryHandlerInterface
{
    /**
     * OperationId: getInventory
     *
     * Returns pet inventories by status
     *
     * Returns a map of status codes to quantities
     */
    public function handle(
    ): GetInventory200ApplicationJsonResponse;
}