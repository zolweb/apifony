<?php

namespace App\Controller;

interface PlaceOrderHandler
{
    public function handle(
        OrderSchema $dto,
    ): PlaceOrder200ApplicationJsonResponse|PlaceOrder405EmptyResponse;
}
