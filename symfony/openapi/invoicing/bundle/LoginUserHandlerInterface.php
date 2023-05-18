<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
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
}
