<?php

namespace App\Controller;

interface CreateUsersWithListInputHandlerInterface
{
    public function handleNullApplicationJson(
    );
    public function handleUserArrayApplicationJson(
            array $content,
    );
}
