<?php

namespace App\Controller;

interface GetOrderByIdHandlerInterface
{
    public function handleNullApplicationJson(
            int $pOrderId,
    );
}
