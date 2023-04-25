<?php

namespace App\Controller;

interface GetOrderByIdHandler
{
    public function handle(
        int $orderId,
    ): GetOrderById200ApplicationJsonResponse|GetOrderById400EmptyResponse|GetOrderById404EmptyResponse;
}
