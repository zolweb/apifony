<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentType;

use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentType;

interface ContentTypeHandler
{
    public function getContentTypeListFromEmptyPayloadToApplicationJsonContent(
    ):
        GetContentTypeList200ApplicationJsonResponse;

    public function getContentTypeFromEmptyPayloadToApplicationJsonContent(
        string $pContentTypeId,
    ):
        GetContentType200ApplicationJsonResponse |
        GetContentType404ApplicationJsonResponse;
}
