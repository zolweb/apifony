<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests;

use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\AtomHandler;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\GetAtom200ApplicationJsonResponse;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\GetAtom200ApplicationJsonResponsePayload;

class TestHandler implements AtomHandler
{
    public function getAtomFromEmptyPayloadToApplicationJsonContent(string $pAtomId): GetAtom200ApplicationJsonResponse
    {
        return new GetAtom200ApplicationJsonResponse(
            new GetAtom200ApplicationJsonResponsePayload(
                $pAtomId,
            ),
        );
    }
}