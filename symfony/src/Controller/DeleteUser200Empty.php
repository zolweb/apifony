<?php

namespace App\Controller;

class DeleteUser200Empty{
    public const CODE = '200';
    public const CONTENT_TYPE = null;

    public function __construct(
    ) {
    }

    /**
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}