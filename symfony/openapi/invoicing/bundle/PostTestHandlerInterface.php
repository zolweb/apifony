<?php

namespace App\Controller;

interface PostTestHandlerInterface
{
    public function handleEmptyPayloadToApplicationJsonContent(
        string $cC1,
        int $cC2,
        float $cC3,
        bool $cC4,
        string $hH1,
        int $hH2,
        float $hH3,
        bool $hH4,
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
        string $qQ1,
        int $qQ2,
        float $qQ3,
        bool $qQ4,
    ):
        PostTest200ApplicationJson |
        PostTest201ApplicationJson;

    public function handleTestPayloadToApplicationJsonContent(
        string $cC1,
        int $cC2,
        float $cC3,
        bool $cC4,
        string $hH1,
        int $hH2,
        float $hH3,
        bool $hH4,
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
        string $qQ1,
        int $qQ2,
        float $qQ3,
        bool $qQ4,
        Test $requestBodyPayload,
    ):
        PostTest200ApplicationJson |
        PostTest201ApplicationJson;
}
