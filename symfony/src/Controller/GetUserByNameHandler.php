<?php

namespace App\Controller;

interface GetUserByNameHandler
{
    public function handle(
        string $username,
    ): GetUserByName200ApplicationJsonResponse|GetUserByName200ApplicationXmlResponse|GetUserByName400EmptyResponse|GetUserByName404EmptyResponse;
}
