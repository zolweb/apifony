<?php

namespace App\Controller;

interface DeleteOrderHandlerInterface
{
    /**
     * OperationId: deleteOrder
     */
    public function handle(
        int $orderId
    ): ;
}
