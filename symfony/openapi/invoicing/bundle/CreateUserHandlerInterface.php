<?php

namespace App\Controller;

interface CreateUserHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJson;

    public function handleUserPayloadToApplicationJsonContent(
        User $requestBodyPayload,
    ):
        CreateUser201ApplicationJson;
}
