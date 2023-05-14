<?php

namespace App\Controller;

interface PlaceOrderHandlerInterface
{
    public function handle(
        Lol $payload,
    ) ;
}
