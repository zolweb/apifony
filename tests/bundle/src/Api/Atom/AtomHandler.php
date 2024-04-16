<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;


interface AtomHandler
{
    public function getAtomFromEmptyPayloadToApplicationJsonContent(
        string $pAtomId,
    ):
        GetAtom200ApplicationJsonResponse;
}
