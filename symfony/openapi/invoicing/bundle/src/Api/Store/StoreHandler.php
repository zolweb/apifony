<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

use App\Zol\Invoicing\Presentation\Api\Bundle\Model\Order;

interface StoreHandler
{
    public function getInventoryFromEmptyPayloadToApplicationJsonContent(
    ):
        GetInventory200ApplicationJsonResponse;

    public function placeOrderFromEmptyPayloadToApplicationJsonContent(
    ):
        PlaceOrder200ApplicationJsonResponse;

    public function placeOrderFromEmptyPayloadToContent(
    ):
        PlaceOrder405EmptyResponse;

    public function placeOrderFromOrderPayloadToApplicationJsonContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder200ApplicationJsonResponse;

    public function placeOrderFromOrderPayloadToContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder405EmptyResponse;

    public function getOrderByIdFromEmptyPayloadToApplicationJsonContent(
        int $pOrderId,
    ):
        GetOrderById200ApplicationJsonResponse;

    public function getOrderByIdFromEmptyPayloadToContent(
        int $pOrderId,
    ):
        GetOrderById400EmptyResponse |
        GetOrderById404EmptyResponse;

    public function deleteOrderFromEmptyPayloadToContent(
        int $pOrderId,
    ):
        DeleteOrder200EmptyResponse |
        DeleteOrder400EmptyResponse |
        DeleteOrder404EmptyResponse;
}
