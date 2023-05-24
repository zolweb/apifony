<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

interface UserHandler
{
    public function CreateUserFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJson;

    public function CreateUserFromCreateUserApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        CreateUserApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        CreateUser201ApplicationJson;

    public function CreateUsersWithListInputFromEmptyPayloadToContent(
    ):
        CreateUsersWithListInput100Empty;

    public function CreateUsersWithListInputFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUsersWithListInput200ApplicationJson;

    public function CreateUsersWithListInputFromUserArrayPayloadToContent(
        array<User> $requestBodyPayload,
    ):
        CreateUsersWithListInput100Empty;

    public function CreateUsersWithListInputFromUserArrayPayloadToApplicationJsonContent(
        array<User> $requestBodyPayload,
    ):
        CreateUsersWithListInput200ApplicationJson;

    public function LoginUserFromEmptyPayloadToApplicationJsonContent(
        string $qusername,
        string $qpassword,
    ):
        LoginUser200ApplicationJson;

    public function LoginUserFromEmptyPayloadToContent(
        string $qusername,
        string $qpassword,
    ):
        LoginUser400Empty;

    public function LogoutUserFromEmptyPayloadToContent(
    ):
        LogoutUser100Empty |
        LogoutUser200Empty;

    public function GetUserByNameFromEmptyPayloadToApplicationJsonContent(
        string $pusername,
    ):
        GetUserByName200ApplicationJson;

    public function GetUserByNameFromEmptyPayloadToContent(
        string $pusername,
    ):
        GetUserByName400Empty |
        GetUserByName404Empty;

    public function UpdateUserFromEmptyPayloadToContent(
        string $pusername,
    ):
        UpdateUser201Empty;

    public function UpdateUserFromUpdateUserApplicationJsonRequestBodyPayloadPayloadToContent(
        string $pusername,
        UpdateUserApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdateUser201Empty;

    public function DeleteUserFromEmptyPayloadToContent(
        string $pusername,
    ):
        DeleteUser200Empty |
        DeleteUser400Empty |
        DeleteUser404Empty;
}
