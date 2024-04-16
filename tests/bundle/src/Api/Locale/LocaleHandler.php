<?php

namespace Zol\TestOpenApiServer\Api\Locale;


interface LocaleHandler
{
    public function getLocaleListFromEmptyPayloadToApplicationJsonContent(
    ):
        GetLocaleList200ApplicationJsonResponse;
}
