<?php

namespace App\Controller;

interface DeleteOrderHandler
{
    public function handle(
        int $orderId,
    ): void;
}
