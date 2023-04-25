<?php

namespace App\Controller;

interface DeleteOrderHandler
{
    public function handle(
        int $orderId,
    ): DeleteOrder400EmptyResponse|DeleteOrder404EmptyResponse;
}
