<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

interface UserHandler
{
    public function CreateUserFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJsonResponse;

    public function CreateUserFromCreateUserApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        CreateUserApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        CreateUser201ApplicationJsonResponse;

    public function CreateUsersWithListInputFromEmptyPayloadToContent(
    ):
        CreateUsersWithListInput100EmptyResponse;

    public function CreateUsersWithListInputFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUsersWithListInput200ApplicationJsonResponse;

    public function CreateUsersWithListInputFromUserArrayPayloadToContent(
        array<User> $requestBodyPayload,
    ):
        CreateUsersWithListInput100EmptyResponse;

    public function CreateUsersWithListInputFromUserArrayPayloadToApplicationJsonContent(
        array<User> $requestBodyPayload,
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

    public function UpdateUserFromUpdateUserApplicationJsonRequestBodyPayloadPayloadToContent(
        string $pUsername,
        UpdateUserApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdateUser201EmptyResponse;

    public function UpdateUserFromUpdateUserApplicationXmlRequestBodyPayloadPayloadToContent(
        string $pUsername,
        UpdateUserApplicationXmlRequestBodyPayload $requestBodyPayload,
    ):
        UpdateUser201EmptyResponse;

    public function UpdateUserFromUpdateUserApplicationXWwwFormUrlencodedRequestBodyPayloadPayloadToContent(
        string $pUsername,
        UpdateUserApplicationXWwwFormUrlencodedRequestBodyPayload $requestBodyPayload,
    ):
        UpdateUser201EmptyResponse;

    public function DeleteUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        DeleteUser200EmptyResponse |
        DeleteUser400EmptyResponse |
        DeleteUser404EmptyResponse;
}
