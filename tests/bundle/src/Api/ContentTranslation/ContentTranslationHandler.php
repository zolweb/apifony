<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentTranslation;

interface ContentTranslationHandler
{
    public function postContentTranslationFromPostContentTranslationApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        PostContentTranslationApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PostContentTranslation201EmptyResponse |
        PostContentTranslation400ApplicationJsonResponse |
        PostContentTranslation409ApplicationJsonResponse;

    public function patchContentTranslationFromPatchContentTranslationApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pContentTranslationId,
        PatchContentTranslationApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PatchContentTranslation200EmptyResponse |
        PatchContentTranslation400ApplicationJsonResponse |
        PatchContentTranslation404ApplicationJsonResponse |
        PatchContentTranslation409ApplicationJsonResponse;

    public function getContentTranslationFromEmptyPayloadToApplicationJsonContent(
        string $pContentTranslationId,
    ):
        GetContentTranslation200ApplicationJsonResponse |
        GetContentTranslation404ApplicationJsonResponse;

    public function getContentTranslationDataVersionsFromEmptyPayloadToApplicationJsonContent(
        string $pContentTranslationId,
        int $qPageNumber,
        int $qPageSize,
    ):
        GetContentTranslationDataVersions200ApplicationJsonResponse;

    public function publishContentTranslationFromPublishContentTranslationApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pContentTranslationId,
        PublishContentTranslationApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        PublishContentTranslation200EmptyResponse |
        PublishContentTranslation400ApplicationJsonResponse |
        PublishContentTranslation404ApplicationJsonResponse |
        PublishContentTranslation409ApplicationJsonResponse;

    public function withdrawContentTranslationFromWithdrawContentTranslationApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
        string $pContentTranslationId,
        WithdrawContentTranslationApplicationJsonRequestBodyPayload $requestBodyPayload,
    ):
        WithdrawContentTranslation200EmptyResponse |
        WithdrawContentTranslation400ApplicationJsonResponse |
        WithdrawContentTranslation404ApplicationJsonResponse |
        WithdrawContentTranslation409ApplicationJsonResponse;
}
