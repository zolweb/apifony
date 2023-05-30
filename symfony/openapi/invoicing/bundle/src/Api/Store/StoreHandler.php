<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

interface StoreHandler
{
    public function GetInventoryFromEmptyPayloadToContent(
    ):
        GetInventory200EmptyResponse;

    public function PlaceOrderFromEmptyPayloadToContent(
    ):
        PlaceOrder200EmptyResponse |
        PlaceOrder405EmptyResponse;

    public function GetOrderByIdFromEmptyPayloadToContent(
        int $porderId,
    ):
        GetOrderById200EmptyResponse |
        GetOrderById400EmptyResponse |
        GetOrderById404EmptyResponse;

    public function DeleteOrderFromEmptyPayloadToContent(
        int $porderId,
    ):
        DeleteOrder200EmptyResponse |
        DeleteOrder400EmptyResponse |
        DeleteOrder404EmptyResponse;
}
