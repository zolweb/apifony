<?php

namespace App\Controller;

interface UpdateUserHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        UpdateUser201Empty;

    public function handleUserPayloadToEmptyContent(
        string $pUsername,
        User $requestBodyPayload,
    ):
        UpdateUser201Empty;
}
