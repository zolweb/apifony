<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests;

use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\AtomHandler;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\GetAtom200ApplicationJsonResponse;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\GetAtom200ApplicationJsonResponsePayload;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\PostAtom201EmptyResponse;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\PostAtom400ApplicationJsonResponse;
use Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\PostAtomApplicationJsonRequestBodyPayload;

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

    public function postAtomFromPostAtomApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(PostAtomApplicationJsonRequestBodyPayload $requestBodyPayload): PostAtom201EmptyResponse|PostAtom400ApplicationJsonResponse
    {
        return new PostAtom201EmptyResponse();
    }
}