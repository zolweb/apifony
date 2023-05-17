<?php

namespace App\Controller;

interface GetOrderByIdHandlerInterface
{
    public function handleEmptyApplicationJson(
        int $pOrderId,
    ):
        GetOrderById200Order;
}
