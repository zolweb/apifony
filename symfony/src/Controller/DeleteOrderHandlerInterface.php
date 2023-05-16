<?php

namespace App\Controller;

interface DeleteOrderHandlerInterface
{
    public function handle(
        int $pOrderId,
    ) ;
}
