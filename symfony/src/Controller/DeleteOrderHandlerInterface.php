<?php

namespace App\Controller;

interface DeleteOrderHandlerInterface
{
    /**
     * OperationId: deleteOrder
     *
     * Delete purchase order by ID
     *
     * For valid response try integer IDs with value < 1000. Anything above 1000 or nonintegers will generate API errors
     */
    public function handle(
        int $orderId = null,
    ): DeleteOrder400EmptyResponse|DeleteOrder404EmptyResponse;
}
