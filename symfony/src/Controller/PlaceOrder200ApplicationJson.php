<?php

namespace App\Controller;

class PlaceOrder200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly Order $content,
    ) {
    }
}