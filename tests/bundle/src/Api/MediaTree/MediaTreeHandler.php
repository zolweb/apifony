<?php

namespace Zol\TestOpenApiServer\Api\MediaTree;

use Zol\TestOpenApiServer\Model\MediaTree;
use Zol\TestOpenApiServer\Model\MediaFolder;

interface MediaTreeHandler
{
    public function getMediaTreeFromEmptyPayloadToApplicationJsonContent(
        string $qPath,
    ):
        GetMediaTree200ApplicationJsonResponse;

    public function moveToMediaFolderFromMoveToMediaFolderApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        MoveToMediaFolderApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        MoveToMediaFolder200EmptyResponse |
        MoveToMediaFolder400ApplicationJsonResponse |
        MoveToMediaFolder404ApplicationJsonResponse |
        MoveToMediaFolder409ApplicationJsonResponse;

    public function addMediaFolderFromAddMediaFolderApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        AddMediaFolderApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        AddMediaFolder201EmptyResponse |
        AddMediaFolder400ApplicationJsonResponse |
        AddMediaFolder404ApplicationJsonResponse |
        AddMediaFolder409ApplicationJsonResponse;

    public function getMediaFolderFromEmptyPayloadToApplicationJsonContent(
        string $pMediaFolderId,
    ):
        GetMediaFolder200ApplicationJsonResponse |
        GetMediaFolder404ApplicationJsonResponse;

    public function updateMediaFolderFromUpdateMediaFolderApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pMediaFolderId,
        UpdateMediaFolderApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        UpdateMediaFolder200EmptyResponse |
        UpdateMediaFolder400ApplicationJsonResponse |
        UpdateMediaFolder404ApplicationJsonResponse |
        UpdateMediaFolder409ApplicationJsonResponse;

    public function deleteMediaFolderFromDeleteMediaFolderApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pMediaFolderId,
        DeleteMediaFolderApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        DeleteMediaFolder200EmptyResponse |
        DeleteMediaFolder404ApplicationJsonResponse |
        DeleteMediaFolder409ApplicationJsonResponse;
}
