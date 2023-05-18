<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        string $pUsername,
    ):
        GetUserByName200ApplicationJson;
    public function handleEmptyPayloadToEmptyContent(
        string $pUsername,
    ):
        GetUserByName400Empty |
        GetUserByName404Empty;
}
