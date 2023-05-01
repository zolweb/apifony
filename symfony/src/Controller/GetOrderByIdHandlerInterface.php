<?php

namespace App\Controller;

interface GetOrderByIdHandlerInterface
{
    /**
     * OperationId: getOrderById
     *
     * Find purchase order by ID
     *
     * For valid response try integer IDs with value <= 5 or > 10. Other values will generate exceptions.
     */
    public function handle(
        int $pOrderId = null,
    ): GetOrderById200ApplicationJsonResponse|GetOrderById400EmptyResponse|GetOrderById404EmptyResponse;
}
