<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

use App\Zol\Invoicing\Presentation\Api\Bundle\Model\User;

interface UserHandler
{
    public function CreateUserFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJsonResponse;

    public function CreateUserFromUserPayloadToApplicationJsonContent(
        User $requestBodyPayload,
    ):
        CreateUser201ApplicationJsonResponse;

    public function CreateUsersWithListInputFromEmptyPayloadToContent(
    ):
        CreateUsersWithListInput100EmptyResponse;

    public function CreateUsersWithListInputFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUsersWithListInput200ApplicationJsonResponse;

    public function LoginUserFromEmptyPayloadToApplicationJsonContent(
        string $qUsername,
        string $qPassword,
    ):
        LoginUser200ApplicationJsonResponse;

    public function LoginUserFromEmptyPayloadToContent(
        string $qUsername,
        string $qPassword,
    ):
        LoginUser400EmptyResponse;

    public function LogoutUserFromEmptyPayloadToContent(
    ):
        LogoutUser100EmptyResponse |
        LogoutUser200EmptyResponse;

    public function GetUserByNameFromEmptyPayloadToApplicationJsonContent(
        string $pUsername,
    ):
        GetUserByName200ApplicationJsonResponse;

    public function GetUserByNameFromEmptyPayloadToApplicationXmlContent(
        string $pUsername,
    ):
        GetUserByName200ApplicationXmlResponse;

    public function GetUserByNameFromEmptyPayloadToContent(
        string $pUsername,
    ):
        GetUserByName400EmptyResponse |
        GetUserByName404EmptyResponse;

    public function UpdateUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        UpdateUser201EmptyResponse;

    public function UpdateUserFromUserPayloadToContent(
        string $pUsername,
        User $requestBodyPayload,
    ):
        UpdateUser201EmptyResponse;

    public function DeleteUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        DeleteUser200EmptyResponse |
        DeleteUser400EmptyResponse |
        DeleteUser404EmptyResponse;
}
