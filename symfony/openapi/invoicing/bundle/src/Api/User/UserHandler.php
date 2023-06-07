<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

interface UserHandler
{
    public function CreateUserFromEmptyPayloadToContent(
    ):
        CreateUser201EmptyResponse;

    public function CreateUsersWithListInputFromEmptyPayloadToContent(
    ):
        CreateUsersWithListInput100EmptyResponse |
        CreateUsersWithListInput200EmptyResponse;

    public function LoginUserFromEmptyPayloadToContent(
        string $qUsername,
        string $qPassword,
    ):
        LoginUser200EmptyResponse |
        LoginUser400EmptyResponse;

    public function LogoutUserFromEmptyPayloadToContent(
    ):
        LogoutUser100EmptyResponse |
        LogoutUser200EmptyResponse;

    public function GetUserByNameFromEmptyPayloadToContent(
        string $pUsername,
    ):
        GetUserByName200EmptyResponse |
        GetUserByName400EmptyResponse |
        GetUserByName404EmptyResponse;

    public function UpdateUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        UpdateUser201EmptyResponse;

    public function DeleteUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        DeleteUser200EmptyResponse |
        DeleteUser400EmptyResponse |
        DeleteUser404EmptyResponse;
}
