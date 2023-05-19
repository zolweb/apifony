<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Store;

interface StoreHandlerInterface
{
    public function getInventoryFromEmptyPayloadToApplicationJsonContent(
    ):
        GetInventory200ApplicationJson;

    public function placeOrderFromEmptyPayloadToApplicationJsonContent(
    ):
        PlaceOrder200ApplicationJson;
    public function placeOrderFromEmptyPayloadToEmptyContent(
    ):
        PlaceOrder405Empty;

    public function placeOrderFromOrderPayloadToApplicationJsonContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder200ApplicationJson;
    public function placeOrderFromOrderPayloadToEmptyContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder405Empty;

    public function getOrderByIdFromEmptyPayloadToApplicationJsonContent(
        int $pOrderId,
    ):
        GetOrderById200ApplicationJson;
    public function getOrderByIdFromEmptyPayloadToEmptyContent(
        int $pOrderId,
    ):
        GetOrderById400Empty |
        GetOrderById404Empty;

    public function deleteOrderFromEmptyPayloadToEmptyContent(
        int $pOrderId,
    ):
        DeleteOrder200Empty |
        DeleteOrder400Empty |
        DeleteOrder404Empty;
}
