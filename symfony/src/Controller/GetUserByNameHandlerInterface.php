<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    public function handleEmptyApplicationJson(
        string $pUsername,
    ):
        GetUserByName200User;
}
