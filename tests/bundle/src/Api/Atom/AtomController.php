<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
class AtomController extends AbstractController
{
    private AtomHandler $handler;
    public function setHandler(AtomHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function getAtom(Request $request, string $atomId): Response
    {
        $errors = [];
        $pAtomId = $atomId;
        try {
            $this->validateParameter($pAtomId, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['atomId'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case '':
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case is_null($requestBodyPayload):
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getAtomFromEmptyPayloadToApplicationJsonContent($pAtomId);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
            default:
                throw new \RuntimeException();
        }
    }
    public function postAtom(Request $request): Response
    {
        $errors = [];
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, PostAtomApplicationJsonRequestBodyPayload::class);
                    $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->messages];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_request_type', "The value '{$requestBodyPayloadContentType}' received in content-type header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        if (count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case $requestBodyPayload instanceof PostAtomApplicationJsonRequestBodyPayload:
                switch ($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->postAtomFromPostAtomApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent($requestBodyPayload);
                        break;
                    default:
                        return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                }
            default:
                throw new \RuntimeException();
        }
    }
}