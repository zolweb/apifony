<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

use App\Zol\Invoicing\Presentation\Api\Bundle\Model\Order;

interface StoreHandler
{
    public function GetInventoryFromEmptyPayloadToApplicationJsonContent(
    ):
        GetInventory200ApplicationJsonResponse;

    public function PlaceOrderFromEmptyPayloadToApplicationJsonContent(
    ):
        PlaceOrder200ApplicationJsonResponse;

    public function PlaceOrderFromEmptyPayloadToContent(
    ):
        PlaceOrder405EmptyResponse;

    public function PlaceOrderFromOrderPayloadToApplicationJsonContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder200ApplicationJsonResponse;

    public function PlaceOrderFromOrderPayloadToContent(
        Order $requestBodyPayload,
    ):
        PlaceOrder405EmptyResponse;

    public function GetOrderByIdFromEmptyPayloadToApplicationJsonContent(
        int $pOrderId,
    ):
        GetOrderById200ApplicationJsonResponse;

    public function GetOrderByIdFromEmptyPayloadToContent(
        int $pOrderId,
    ):
        GetOrderById400EmptyResponse |
        GetOrderById404EmptyResponse;

    public function DeleteOrderFromEmptyPayloadToContent(
        int $pOrderId,
    ):
        DeleteOrder200EmptyResponse |
        DeleteOrder400EmptyResponse |
        DeleteOrder404EmptyResponse;
}
