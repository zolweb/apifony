<?php

namespace Zol\TestOpenApiServer\Api\Content;

use Zol\TestOpenApiServer\Api\DenormalizationException;
use Zol\TestOpenApiServer\Api\ParameterValidationException;
use Zol\TestOpenApiServer\Api\RequestBodyValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Api\AbstractController;
use Zol\TestOpenApiServer\Format\Uuid as AssertUuid;
use Zol\TestOpenApiServer\Model\Content;

class ContentController extends AbstractController
{
    private ContentHandler $handler;

    public function setHandler(ContentHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function postContent(
        Request $request,
    ): Response {
        $errors = [];

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, PostContentApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody(
                        $requestBodyPayload,
                        [
                            new Assert\Valid,
                            new Assert\NotNull,
                        ],
                    );
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case $requestBodyPayload instanceOf PostContentApplicationJsonRequestBodyPayload:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->postContentFromPostContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function getContentList(
        Request $request,
    ): Response {
        $errors = [];

        $qContentTypeId = '';
        try {
            $qContentTypeId = $this->getStringOrNullParameter($request, 'contentTypeId', 'query', false);
            $this->validateParameter(
                $qContentTypeId,
                [
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['contentTypeId'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['contentTypeId'] = $e->messages;
        }

        $qPageNumber = 0;
        try {
            $qPageNumber = $this->getIntParameter($request, 'pageNumber', 'query', true);
            $this->validateParameter(
                $qPageNumber,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['pageNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['pageNumber'] = $e->messages;
        }

        $qPageSize = 0;
        try {
            $qPageSize = $this->getIntParameter($request, 'pageSize', 'query', true);
            $this->validateParameter(
                $qPageSize,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['pageSize'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['pageSize'] = $e->messages;
        }

        $qSort = '';
        try {
            $qSort = $this->getStringOrNullParameter($request, 'sort', 'query', false);
            $this->validateParameter(
                $qSort,
                [
                    new Assert\Regex(pattern: '/^(creationTimestamp|name)(:(asc|desc))?$/'),
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['sort'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['sort'] = $e->messages;
        }

        $qStatus = '';
        try {
            $qStatus = $this->getStringOrNullParameter($request, 'status', 'query', false);
            $this->validateParameter(
                $qStatus,
                [
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['status'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['status'] = $e->messages;
        }

        $qSearch = '';
        try {
            $qSearch = $this->getStringOrNullParameter($request, 'search', 'query', false);
            $this->validateParameter(
                $qSearch,
                [
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['search'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['search'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':


                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getContentListFromEmptyPayloadToApplicationJsonContent(
                            $qContentTypeId,
                            $qPageNumber,
                            $qPageSize,
                            $qSort,
                            $qStatus,
                            $qSearch,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function getContent(
        Request $request,
        string $contentId,
    ): Response {
        $errors = [];

        $pContentId = $contentId;
        try {
            $this->validateParameter(
                $pContentId,
                [
                    new Assert\NotNull,
                    new AssertUuid,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':


                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getContentFromEmptyPayloadToApplicationJsonContent(
                            $pContentId,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function patchContent(
        Request $request,
        string $contentId,
    ): Response {
        $errors = [];

        $pContentId = $contentId;
        try {
            $this->validateParameter(
                $pContentId,
                [
                    new Assert\NotNull,
                    new AssertUuid,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, PatchContentApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody(
                        $requestBodyPayload,
                        [
                            new Assert\Valid,
                            new Assert\NotNull,
                        ],
                    );
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case $requestBodyPayload instanceOf PatchContentApplicationJsonRequestBodyPayload:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->patchContentFromPatchContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
                            $pContentId,
                            $requestBodyPayload,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function archiveContent(
        Request $request,
        string $contentId,
    ): Response {
        $errors = [];

        $pContentId = $contentId;
        try {
            $this->validateParameter(
                $pContentId,
                [
                    new Assert\NotNull,
                    new AssertUuid,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, ArchiveContentApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody(
                        $requestBodyPayload,
                        [
                            new Assert\Valid,
                            new Assert\NotNull,
                        ],
                    );
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case $requestBodyPayload instanceOf ArchiveContentApplicationJsonRequestBodyPayload:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->archiveContentFromArchiveContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
                            $pContentId,
                            $requestBodyPayload,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function restoreContent(
        Request $request,
        string $contentId,
    ): Response {
        $errors = [];

        $pContentId = $contentId;
        try {
            $this->validateParameter(
                $pContentId,
                [
                    new Assert\NotNull,
                    new AssertUuid,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, RestoreContentApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody(
                        $requestBodyPayload,
                        [
                            new Assert\Valid,
                            new Assert\NotNull,
                        ],
                    );
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case $requestBodyPayload instanceOf RestoreContentApplicationJsonRequestBodyPayload:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->restoreContentFromRestoreContentApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
                            $pContentId,
                            $requestBodyPayload,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function duplicateContent(
        Request $request,
        string $contentId,
    ): Response {
        $errors = [];

        $pContentId = $contentId;
        try {
            $this->validateParameter(
                $pContentId,
                [
                    new Assert\NotNull,
                    new AssertUuid,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':


                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }

        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }

        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->duplicateContentFromEmptyPayloadToApplicationJsonContent(
                            $pContentId,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }
}
