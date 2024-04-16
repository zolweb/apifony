<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Locale;


interface LocaleHandler
{
    public function getLocaleListFromEmptyPayloadToApplicationJsonContent(
    ):
        GetLocaleList200ApplicationJsonResponse;
}
