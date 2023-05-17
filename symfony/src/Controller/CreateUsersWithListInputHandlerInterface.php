<?php

namespace App\Controller;

interface CreateUsersWithListInputHandlerInterface
{
    public function handleEmptyApplicationJson(
    ):
        CreateUsersWithListInput200User;

    public function handleUserArrayApplicationJson(
        array $content,
    ):
        CreateUsersWithListInput200User;
}
