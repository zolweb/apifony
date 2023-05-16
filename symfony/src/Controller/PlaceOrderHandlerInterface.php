<?php

namespace App\Controller;

interface PlaceOrderHandlerInterface
{
    public function handleNullApplicationJson(
    );
    public function handleOrderApplicationJson(
            Order $content,
    );
}
