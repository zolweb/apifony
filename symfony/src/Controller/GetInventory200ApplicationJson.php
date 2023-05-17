<?php

namespace App\Controller;

class GetInventory200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly GetInventory200ApplicationJsonSchema $content,
    ) {
    }
}