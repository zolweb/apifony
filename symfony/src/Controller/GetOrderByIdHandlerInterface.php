<?php

namespace App\Controller;

interface GetOrderByIdHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        int $pOrderId,
    ):
        GetOrderById200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        int $pOrderId,
    ):
        GetOrderById400Empty |
        GetOrderById404Empty;
}
