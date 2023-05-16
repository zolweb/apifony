<?php

namespace App\Controller;

interface GetClientHandlerInterface
{
    public function handleNullApplicationJson(
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
    );
    public function handleIntegerApplicationJson(
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
            int $content,
    );
}
