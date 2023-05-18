<?php

namespace App\Controller;

interface CreateUsersWithListInputHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
    ):
        CreateUsersWithListInput100Empty;
    public function handleEmptyPayloadToApplicationJsonContent(
    ):
        CreateUsersWithListInput200ApplicationJson;

    public function handleUserArrayPayloadToEmptyContent(
        array $requestBodyPayload,
    ):
        CreateUsersWithListInput100Empty;
    public function handleUserArrayPayloadToApplicationJsonContent(
        array $requestBodyPayload,
    ):
        CreateUsersWithListInput200ApplicationJson;
}
