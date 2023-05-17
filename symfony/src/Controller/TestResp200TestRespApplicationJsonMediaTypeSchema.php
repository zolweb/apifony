<?php

namespace App\Controller;

class TestResp200TestRespApplicationJsonMediaTypeSchema
{
    public const code = '200';

    public function __construct(
        public readonly TestRespApplicationJsonMediaTypeSchema $content,
    ) {
    }
}