<?php

namespace App\Controller;

class GetInventory200GetInventoryApplicationJsonMediaTypeSchema
{
    public const code = '200';

    public function __construct(
        public readonly GetInventoryApplicationJsonMediaTypeSchema $content,
    ) {
    }
}