<?php

namespace App\Controller;

interface DeleteOrderHandlerInterface
{
    public function handleEmptyPayloadToEmptyContent(
        int $pOrderId,
    ):
        DeleteOrder200Empty |
        DeleteOrder400Empty |
        DeleteOrder404Empty;
}
