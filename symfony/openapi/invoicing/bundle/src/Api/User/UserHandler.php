<?php

namespace AppZolInvoicingPresentationApiBundle\Api\User;

use AppZolInvoicingPresentationApiBundle\Model\User;

interface UserHandler
{
    public function createUserFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUser201ApplicationJsonResponse;

    public function createUserFromUserPayloadToApplicationJsonContent(
        User $requestBodyPayload,
    ):
        CreateUser201ApplicationJsonResponse;

    public function createUsersWithListInputFromEmptyPayloadToContent(
    ):
        CreateUsersWithListInput100EmptyResponse;

    public function createUsersWithListInputFromEmptyPayloadToApplicationJsonContent(
    ):
        CreateUsersWithListInput200ApplicationJsonResponse;

    public function loginUserFromEmptyPayloadToApplicationJsonContent(
        string $qUsername,
        string $qPassword,
    ):
        LoginUser200ApplicationJsonResponse;

    public function loginUserFromEmptyPayloadToContent(
        string $qUsername,
        string $qPassword,
    ):
        LoginUser400EmptyResponse;

    public function logoutUserFromEmptyPayloadToContent(
    ):
        LogoutUser100EmptyResponse |
        LogoutUser200EmptyResponse;

    public function getUserByNameFromEmptyPayloadToApplicationJsonContent(
        string $pUsername,
    ):
        GetUserByName200ApplicationJsonResponse;

    public function getUserByNameFromEmptyPayloadToContent(
        string $pUsername,
    ):
        GetUserByName400EmptyResponse |
        GetUserByName404EmptyResponse;

    public function updateUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        UpdateUser201EmptyResponse;

    public function updateUserFromUserPayloadToContent(
        string $pUsername,
        User $requestBodyPayload,
    ):
        UpdateUser201EmptyResponse;

    public function deleteUserFromEmptyPayloadToContent(
        string $pUsername,
    ):
        DeleteUser200EmptyResponse |
        DeleteUser400EmptyResponse |
        DeleteUser404EmptyResponse;
}
