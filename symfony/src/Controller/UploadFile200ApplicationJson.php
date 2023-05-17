<?php

namespace App\Controller;

class UploadFile200ApplicationJson{
    public const code = '200';

    public function __construct(
        public readonly ApiResponse $content,
    ) {
    }
}