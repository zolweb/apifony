<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

interface StoreHandler
{
    public function GetInventoryFromEmptyPayloadToApplicationJsonContent(
    ):
        GetInventory200ApplicationJson;

    public function PlaceOrderFromEmptyPayloadToApplicationJsonContent(
    ):
        PlaceOrder200ApplicationJson;

    public function PlaceOrderFromEmptyPayloadToContent(
    ):
        PlaceOrder405Empty;

    public function PlaceOrderFromPlaceOrderApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        PlaceOrderApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PlaceOrder200ApplicationJson;

    public function PlaceOrderFromPlaceOrderApplicationJsonRequestBodyPayloadPayloadToContent(
        PlaceOrderApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PlaceOrder405Empty;

    public function GetOrderByIdFromEmptyPayloadToApplicationJsonContent(
        int $porderId,
    ):
        GetOrderById200ApplicationJson;

    public function GetOrderByIdFromEmptyPayloadToContent(
        int $porderId,
    ):
        GetOrderById400Empty |
        GetOrderById404Empty;

    public function DeleteOrderFromEmptyPayloadToContent(
        int $porderId,
    ):
        DeleteOrder200Empty |
        DeleteOrder400Empty |
        DeleteOrder404Empty;
}
