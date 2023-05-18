<?php

namespace App\Controller;

interface StoreHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
    ):
        GetInventory200ApplicationJson;

    public function handleEmptyPayloadToApplicationJsonContent(
    ):
        PlaceOrder200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
    ):
        PlaceOrder405Empty;

    public function handleOrderPayloadToApplicationJsonContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder200ApplicationJson;
    public function handleOrderPayloadToEmptyContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder405Empty;

    public function handleEmptyPayloadToApplicationJsonContent(
        int $pOrderId,
    ):
        GetOrderById200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        int $pOrderId,
    ):
        GetOrderById400Empty |
        GetOrderById404Empty;

    public function handleEmptyPayloadToEmptyContent(
        int $pOrderId,
    ):
        DeleteOrder200Empty |
        DeleteOrder400Empty |
        DeleteOrder404Empty;
}
