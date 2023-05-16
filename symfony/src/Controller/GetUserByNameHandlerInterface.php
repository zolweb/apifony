<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    public function handleNullApplicationJson(
            string $pUsername,
    );
}
