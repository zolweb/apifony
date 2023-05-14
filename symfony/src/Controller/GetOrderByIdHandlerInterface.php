<?php

namespace App\Controller;

interface GetOrderByIdHandlerInterface
{
    public function handle(
        int $pOrderId
    ) ;
}
