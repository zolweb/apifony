<?php

namespace App\Controller;

class UploadFile200ApiResponse
{
    public const code = '200';

    public function __construct(
        public readonly ApiResponse $content,
    ) {
    }
}