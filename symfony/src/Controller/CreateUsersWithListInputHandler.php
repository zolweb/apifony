<?php

namespace App\Controller;

interface CreateUsersWithListInputHandler
{
    public function handle(
        CreateUsersWithListInputRequestPayload $dto,
    ): CreateUsersWithListInput100EmptyResponse|CreateUsersWithListInput200ApplicationJsonResponse;
}
