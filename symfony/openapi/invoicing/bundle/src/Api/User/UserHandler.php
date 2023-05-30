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
        string $qusername,
        string $qpassword,
    ):
        LoginUser200EmptyResponse |
        LoginUser400EmptyResponse;

    public function LogoutUserFromEmptyPayloadToContent(
    ):
        LogoutUser100EmptyResponse |
        LogoutUser200EmptyResponse;

    public function GetUserByNameFromEmptyPayloadToContent(
        string $pusername,
    ):
        GetUserByName200EmptyResponse |
        GetUserByName400EmptyResponse |
        GetUserByName404EmptyResponse;

    public function UpdateUserFromEmptyPayloadToContent(
        string $pusername,
    ):
        UpdateUser201EmptyResponse;

    public function DeleteUserFromEmptyPayloadToContent(
        string $pusername,
    ):
        DeleteUser200EmptyResponse |
        DeleteUser400EmptyResponse |
        DeleteUser404EmptyResponse;
}
