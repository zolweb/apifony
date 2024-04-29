<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentType;

use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentType;
interface ContentTypeHandler
{
    public function getContentTypeListFromEmptyPayloadToApplicationJsonContent(): GetContentTypeList200ApplicationJsonResponse.php;
    public function getContentTypeFromEmptyPayloadToApplicationJsonContent(string $pContentTypeId): GetContentType200ApplicationJsonResponse.php|GetContentType404ApplicationJsonResponse.php;
}