<?php

namespace App\Controller;

interface UserHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJson;

    public function handleUserPayloadToApplicationJsonContent(
        User $requestBodyPayload,
    ):
        CreateUser201ApplicationJson;

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

    public function handleEmptyPayloadToApplicationJsonContent(
        string $qPassword,
        string $qUsername,
    ):
        LoginUser200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $qPassword,
        string $qUsername,
    ):
        LoginUser400Empty;

    public function handleEmptyPayloadToEmptyContent(
    ):
        LogoutUser100Empty |
        LogoutUser200Empty;

    public function handleEmptyPayloadToApplicationJsonContent(
        string $pUsername,
    ):
        GetUserByName200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        GetUserByName400Empty |
        GetUserByName404Empty;

    public function handleEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        UpdateUser201Empty;

    public function handleUserPayloadToEmptyContent(
        string $pUsername,
        User $requestBodyPayload,
    ):
        UpdateUser201Empty;

    public function handleEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        DeleteUser200Empty |
        DeleteUser400Empty |
        DeleteUser404Empty;
}
