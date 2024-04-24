<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentType;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentType;
class ContentTypeController extends AbstractController
{
    private ContentTypeHandler $handler;
    public function setHandler(ContentTypeHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function getContentTypeList(Request $request): Response
    {
        $errors = [];
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }
    public function getContentType(Request $request, string $contentTypeId): Response
    {
        $errors = [];
        $pContentTypeId = $contentTypeId;
        try {
            $this->validateParameter($pContentTypeId, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTypeId'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }
}