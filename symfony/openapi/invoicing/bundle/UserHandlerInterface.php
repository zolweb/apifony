<?php

namespace App\Controller;

interface UserHandlerInterface
{
    public function createUserFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJson;

    public function createUserFromUserPayloadToApplicationJsonContent(
        User $requestBodyPayload,
    ):
        CreateUser201ApplicationJson;

    public function createUsersWithListInputFromEmptyPayloadToEmptyContent(
    ):
        CreateUsersWithListInput100Empty;
    public function createUsersWithListInputFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUsersWithListInput200ApplicationJson;

    public function createUsersWithListInputFromUserArrayPayloadToEmptyContent(
        array $requestBodyPayload,
    ):
        CreateUsersWithListInput100Empty;
    public function createUsersWithListInputFromUserArrayPayloadToApplicationJsonContent(
        array $requestBodyPayload,
    ):
        CreateUsersWithListInput200ApplicationJson;

    public function loginUserFromEmptyPayloadToApplicationJsonContent(
        string $qPassword,
        string $qUsername,
    ):
        LoginUser200ApplicationJson;
    public function loginUserFromEmptyPayloadToEmptyContent(
        string $qPassword,
        string $qUsername,
    ):
        LoginUser400Empty;

    public function logoutUserFromEmptyPayloadToEmptyContent(
    ):
        LogoutUser100Empty |
        LogoutUser200Empty;

    public function getUserByNameFromEmptyPayloadToApplicationJsonContent(
        string $pUsername,
    ):
        GetUserByName200ApplicationJson;
    public function getUserByNameFromEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        GetUserByName400Empty |
        GetUserByName404Empty;

    public function updateUserFromEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        UpdateUser201Empty;

    public function updateUserFromUserPayloadToEmptyContent(
        string $pUsername,
        User $requestBodyPayload,
    ):
        UpdateUser201Empty;

    public function deleteUserFromEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        DeleteUser200Empty |
        DeleteUser400Empty |
        DeleteUser404Empty;
}
