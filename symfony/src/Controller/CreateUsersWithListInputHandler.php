<?php

namespace App\Controller;

interface CreateUsersWithListInputHandler
{
    public function handle(
        createUsersWithListInputRequestPayload $dto,
    );
}
