<?php

namespace App\Controller;

interface GetOrderByIdHandlerInterface
{
    /**
     * OperationId: getOrderById
     */
    public function handle(
        ?mixed $pOrderId,
    ): ;
}
