<?php

namespace App\Controller;

interface PlaceOrderHandlerInterface
{
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
}
