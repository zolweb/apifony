<?php

namespace App\Controller;

class TestRespApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly TestRespApplicationJsonSchema $content,
    ) {
    }
}