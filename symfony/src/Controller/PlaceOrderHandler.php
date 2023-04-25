<?php

namespace App\Controller;

interface PlaceOrderHandler
{
    public function handle(
        OrderSchema $dto,
    ): void
}}
