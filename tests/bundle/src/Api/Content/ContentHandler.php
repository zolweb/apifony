<?php

namespace Zol\TestOpenApiServer\Api\Content;

use Zol\TestOpenApiServer\Model\Content;

interface ContentHandler
{
    public function postContentFromPostContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        PostContentApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PostContent201EmptyResponse |
        PostContent400ApplicationJsonResponse |
        PostContent409ApplicationJsonResponse;

    public function getContentListFromEmptyPayloadToApplicationJsonContent(
        ?string $qContentTypeId,
        int $qPageNumber,
        int $qPageSize,
        ?string $qSort,
        ?string $qStatus,
        ?string $qSearch,
    ):
        GetContentList200ApplicationJsonResponse;

    public function getContentFromEmptyPayloadToApplicationJsonContent(
        string $pContentId,
    ):
        GetContent200ApplicationJsonResponse |
        GetContent404ApplicationJsonResponse;

    public function patchContentFromPatchContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pContentId,
        PatchContentApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PatchContent200EmptyResponse |
        PatchContent400ApplicationJsonResponse |
        PatchContent404ApplicationJsonResponse |
        PatchContent409ApplicationJsonResponse;

    public function archiveContentFromArchiveContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pContentId,
        ArchiveContentApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        ArchiveContent200EmptyResponse |
        ArchiveContent400ApplicationJsonResponse |
        ArchiveContent404ApplicationJsonResponse |
        ArchiveContent409ApplicationJsonResponse;

    public function restoreContentFromRestoreContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pContentId,
        RestoreContentApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        RestoreContent200EmptyResponse |
        RestoreContent400ApplicationJsonResponse |
        RestoreContent404ApplicationJsonResponse |
        RestoreContent409ApplicationJsonResponse;

    public function duplicateContentFromEmptyPayloadToApplicationJsonContent(
        string $pContentId,
    ):
        DuplicateContent201EmptyResponse |
        DuplicateContent400ApplicationJsonResponse |
        DuplicateContent404ApplicationJsonResponse;
}
