<?php

namespace App\Controller;

class GetClient201ApplicationJson{
    public const code = '201';

    public function __construct(
        public readonly GetClient201ApplicationJsonSchema $content,
    ) {
    }
}