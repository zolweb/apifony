<?php

namespace App\Controller;

interface GetClientHandlerInterface
{
    public function handle(
        float $qAgrez,
        string $hAzef,
        string $pClientId,
        float $pParam3,
        int $pParam4,
        bool $pParam5,
        int $cAzgrzeg,
        bool $hGegzer,
        string $pParam1,
        string $pParam2,
        Lol $payload,
    ) ;
}
