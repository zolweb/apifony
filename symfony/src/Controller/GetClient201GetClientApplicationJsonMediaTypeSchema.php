<?php

namespace App\Controller;

class GetClient201GetClientApplicationJsonMediaTypeSchema
{
    public const code = '201';

    public function __construct(
        public readonly GetClientApplicationJsonMediaTypeSchema $content,
    ) {
    }
}