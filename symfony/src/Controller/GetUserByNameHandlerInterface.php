<?php

namespace App\Controller;

interface GetUserByNameHandlerInterface
{
    public function handle(
        string $pUsername = ''
    ): ;
}
