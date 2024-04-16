<?php

namespace Zol\TestOpenApiServer\Api\ContentType;

use Zol\TestOpenApiServer\Model\ContentType;

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
