<?php

namespace App\Controller;

interface PlaceOrderHandlerInterface
{
    public function handleEmptyApplicationJson(
    ):
        PlaceOrder200Order;

    public function handleOrderApplicationJson(
        Order $content,
    ):
        PlaceOrder200Order;
}
