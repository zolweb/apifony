<?php

namespace App\Controller;

interface LogoutUserHandler
{
    public function handle(
    ): LogoutUser100EmptyResponse;
}
