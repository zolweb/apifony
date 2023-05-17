<?php

namespace App\Controller;

class GetOrderById200Order
{
    public const code = '200';

    public function __construct(
        public readonly Order $content,
    ) {
    }
}