<?php

namespace App\Controller;

interface LogoutUserHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
    ):
        LogoutUser100Empty |
        LogoutUser200Empty;
}
