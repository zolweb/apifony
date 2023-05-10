<?php

namespace App\Controller;

interface GetTestHandlerInterface
{
    /**
     * OperationId: get-test
     *
     * test
     *
     * desc
     */
    public function handle(
    ): GetTest200EmptyResponse;
}