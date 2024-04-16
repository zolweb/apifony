<?php

namespace Zol\TestOpenApiServer\Api\MediaTree;

use Zol\TestOpenApiServer\Model\MediaFolder;

class GetMediaFolder200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly MediaFolder $payload,
    ) {
    }

    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return [
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
