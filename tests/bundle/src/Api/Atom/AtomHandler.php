<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

interface AtomHandler
{
    public function getAtomFromEmptyPayloadToApplicationJsonContent(string $pAtomId): GetAtom200ApplicationJsonResponse.php;
    public function postAtomFromPostAtomApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(PostAtomApplicationJsonRequestBodyPayload $requestBodyPayload): PostAtom201EmptyResponse.php|PostAtom400ApplicationJsonResponse.php;
}