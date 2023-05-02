<?php

namespace App\Controller;

interface PlaceOrderHandlerInterface
{
    /**
     * OperationId: placeOrder
     *
     * Place an order for a pet
     *
     * Place a new order in the store
     */
    public function handle(
        OrderSchema $payload,
    ): PlaceOrder200ApplicationJsonResponse|PlaceOrder405EmptyResponse;
}