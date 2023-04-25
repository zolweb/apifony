<?php

namespace App\Controller;

interface GetOrderByIdHandler
{
    public function handle(
        int $orderId,
    ): void
}}
