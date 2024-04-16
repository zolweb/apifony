<?php

namespace Zol\TestOpenApiServer\Api\ContentType;

use Zol\TestOpenApiServer\Api\DenormalizationException;
use Zol\TestOpenApiServer\Api\ParameterValidationException;
use Zol\TestOpenApiServer\Api\RequestBodyValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\TestOpenApiServer\Api\AbstractController;
use Zol\TestOpenApiServer\Model\ContentType;

class ContentTypeController extends AbstractController
{
    private ContentTypeHandler $handler;

    public function setHandler(ContentTypeHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function getContentTypeList(
        Request $request,
    ): Response {
        $errors = [];

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
                        $response = $this->handler->getContentTypeListFromEmptyPayloadToApplicationJsonContent(
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

    public function getContentType(
        Request $request,
        string $contentTypeId,
    ): Response {
        $errors = [];

        $pContentTypeId = $contentTypeId;
        try {
            $this->validateParameter(
                $pContentTypeId,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTypeId'] = $e->messages;
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
                        $response = $this->handler->getContentTypeFromEmptyPayloadToApplicationJsonContent(
                            $pContentTypeId,
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
}
