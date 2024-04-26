<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Uuid as AssertUuid;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Media;
class MediaController extends AbstractController
{
    private MediaHandler $handler;
    public function setHandler(MediaHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function getMediaList(Request $request): Response
    {
        $errors = [];
        $qPageNumber = 0;
        try {
            $qPageNumber = $this->getIntParameter($request, 'pageNumber', 'query', true);
            $this->validateParameter($qPageNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['pageNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['pageNumber'] = $e->messages;
        }
        $qPageSize = 0;
        try {
            $qPageSize = $this->getIntParameter($request, 'pageSize', 'query', true);
            $this->validateParameter($qPageSize, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['pageSize'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['pageSize'] = $e->messages;
        }
        $qSort = '';
        try {
            $qSort = $this->getStringOrNullParameter($request, 'sort', 'query', false);
            $this->validateParameter($qSort, [new Assert\Regex(pattern: '/^(creationTimestamp|name)(:(asc|desc))?$/')]);
        } catch (DenormalizationException $e) {
            $errors['query']['sort'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['sort'] = $e->messages;
        }
        $qSearch = '';
        try {
            $qSearch = $this->getStringOrNullParameter($request, 'search', 'query', false);
            $this->validateParameter($qSearch, []);
        } catch (DenormalizationException $e) {
            $errors['query']['search'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['search'] = $e->messages;
        }
        $qFolderId = '';
        try {
            $qFolderId = $this->getStringOrNullParameter($request, 'folderId', 'query', false);
            $this->validateParameter($qFolderId, [new AssertMediaFolderId()]);
        } catch (DenormalizationException $e) {
            $errors['query']['folderId'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['folderId'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', 'message' => "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case is_null($requestBodyPayload):
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getMediaListFromEmptyPayloadToApplicationJsonContent($qPageNumber, $qPageSize, $qSort, $qSearch, $qFolderId);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
                break;
            default:
                throw new \RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new \RuntimeException();
        }
    }
    public function postMedia(Request $request): Response
    {
        $errors = [];
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, PostMediaApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', 'message' => "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case $requestBodyPayload instanceof PostMediaApplicationJsonRequestBodyPayload:
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->postMediaFromPostMediaApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent($requestBodyPayload);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
                break;
            default:
                throw new \RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new \RuntimeException();
        }
    }
    public function getMedia(Request $request, string $mediaId): Response
    {
        $errors = [];
        $pMediaId = $mediaId;
        try {
            $this->validateParameter($pMediaId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['mediaId'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', 'message' => "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case is_null($requestBodyPayload):
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getMediaFromEmptyPayloadToApplicationJsonContent($pMediaId);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
                break;
            default:
                throw new \RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new \RuntimeException();
        }
    }
    public function patchMedia(Request $request, string $mediaId): Response
    {
        $errors = [];
        $pMediaId = $mediaId;
        try {
            $this->validateParameter($pMediaId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['mediaId'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, PatchMediaApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', 'message' => "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case $requestBodyPayload instanceof PatchMediaApplicationJsonRequestBodyPayload:
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->patchMediaFromPatchMediaApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent($pMediaId, $requestBodyPayload);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
                break;
            default:
                throw new \RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new \RuntimeException();
        }
    }
    public function archiveMedia(Request $request, string $mediaId): Response
    {
        $errors = [];
        $pMediaId = $mediaId;
        try {
            $this->validateParameter($pMediaId, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['mediaId'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, ArchiveMediaApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', 'message' => "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case $requestBodyPayload instanceof ArchiveMediaApplicationJsonRequestBodyPayload:
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->archiveMediaFromArchiveMediaApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent($pMediaId, $requestBodyPayload);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
                break;
            default:
                throw new \RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new \RuntimeException();
        }
    }
}