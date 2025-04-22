<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Tag;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Schema;
class TagController extends AbstractController
{
    private TagHandler $handler;
    public function setHandler(TagHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function firstOperation(Request $request, string $pathParamString, float $pathParamNumber, int $pathParamInteger, bool $pathParamBoolean): Response
    {
        $errors = [];
        $pPathParamString = $pathParamString;
        try {
            $this->validateParameter($pPathParamString, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['pathParamString'] = $e->messages;
        }
        $pPathParamNumber = $pathParamNumber;
        try {
            $this->validateParameter($pPathParamNumber, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['pathParamNumber'] = $e->messages;
        }
        $pPathParamInteger = $pathParamInteger;
        try {
            $this->validateParameter($pPathParamInteger, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['pathParamInteger'] = $e->messages;
        }
        $pPathParamBoolean = $pathParamBoolean;
        try {
            $this->validateParameter($pPathParamBoolean, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['pathParamBoolean'] = $e->messages;
        }
        $qQueryParamString = '';
        try {
            $qQueryParamString = $this->getStringParameter($request, 'queryParamString', 'query', true);
            $this->validateParameter($qQueryParamString, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['queryParamString'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['queryParamString'] = $e->messages;
        }
        $qQueryParamNumber = 0.0;
        try {
            $qQueryParamNumber = $this->getFloatParameter($request, 'queryParamNumber', 'query', true);
            $this->validateParameter($qQueryParamNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['queryParamNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['queryParamNumber'] = $e->messages;
        }
        $qQueryParamInteger = 0;
        try {
            $qQueryParamInteger = $this->getIntParameter($request, 'queryParamInteger', 'query', true);
            $this->validateParameter($qQueryParamInteger, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['queryParamInteger'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['queryParamInteger'] = $e->messages;
        }
        $qQueryParamBoolean = false;
        try {
            $qQueryParamBoolean = $this->getBoolParameter($request, 'queryParamBoolean', 'query', true);
            $this->validateParameter($qQueryParamBoolean, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['queryParamBoolean'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['queryParamBoolean'] = $e->messages;
        }
        $hHeaderParamString = '';
        try {
            $hHeaderParamString = $this->getStringParameter($request, 'headerParamString', 'header', true);
            $this->validateParameter($hHeaderParamString, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['header']['headerParamString'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['headerParamString'] = $e->messages;
        }
        $hHeaderParamNumber = 0.0;
        try {
            $hHeaderParamNumber = $this->getFloatParameter($request, 'headerParamNumber', 'header', true);
            $this->validateParameter($hHeaderParamNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['header']['headerParamNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['headerParamNumber'] = $e->messages;
        }
        $hHeaderParamInteger = 0;
        try {
            $hHeaderParamInteger = $this->getIntParameter($request, 'headerParamInteger', 'header', true);
            $this->validateParameter($hHeaderParamInteger, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['header']['headerParamInteger'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['headerParamInteger'] = $e->messages;
        }
        $hHeaderParamBoolean = false;
        try {
            $hHeaderParamBoolean = $this->getBoolParameter($request, 'headerParamBoolean', 'header', true);
            $this->validateParameter($hHeaderParamBoolean, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['header']['headerParamBoolean'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['headerParamBoolean'] = $e->messages;
        }
        $cCookieParamString = '';
        try {
            $cCookieParamString = $this->getStringParameter($request, 'cookieParamString', 'cookie', true);
            $this->validateParameter($cCookieParamString, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['cookie']['cookieParamString'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['cookieParamString'] = $e->messages;
        }
        $cCookieParamNumber = 0.0;
        try {
            $cCookieParamNumber = $this->getFloatParameter($request, 'cookieParamNumber', 'cookie', true);
            $this->validateParameter($cCookieParamNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['cookie']['cookieParamNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['cookieParamNumber'] = $e->messages;
        }
        $cCookieParamInteger = 0;
        try {
            $cCookieParamInteger = $this->getIntParameter($request, 'cookieParamInteger', 'cookie', true);
            $this->validateParameter($cCookieParamInteger, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['cookie']['cookieParamInteger'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['cookieParamInteger'] = $e->messages;
        }
        $cCookieParamBoolean = false;
        try {
            $cCookieParamBoolean = $this->getBoolParameter($request, 'cookieParamBoolean', 'cookie', true);
            $this->validateParameter($cCookieParamBoolean, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['cookie']['cookieParamBoolean'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['cookieParamBoolean'] = $e->messages;
        }
        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', '')) {
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, Schema::class);
                    $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }
                break;
            case 'application/x-www-form-urlencoded':
                try {
                    $requestBodyPayload = $this->getObjectWwwFormUrlEncodedRequestBody($request, FirstOperationApplicationXWwwFormUrlencodedRequestBodyPayload::class);
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
        if (\count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $responsePayloadContentType = $request->headers->get('accept', 'application/json');
        if (str_contains($responsePayloadContentType, '*/*')) {
            $responsePayloadContentType = 'application/json';
        }
        switch (true) {
            case $requestBodyPayload instanceof Schema && $responsePayloadContentType === 'application/json':
                $response = $this->handler->firstOperationFromSchemaPayloadToApplicationJsonContent($pPathParamString, $pPathParamNumber, $pPathParamInteger, $pPathParamBoolean, $qQueryParamString, $qQueryParamNumber, $qQueryParamInteger, $qQueryParamBoolean, $hHeaderParamString, $hHeaderParamNumber, $hHeaderParamInteger, $hHeaderParamBoolean, $cCookieParamString, $cCookieParamNumber, $cCookieParamInteger, $cCookieParamBoolean, $requestBodyPayload);
                break;
            case $requestBodyPayload instanceof FirstOperationApplicationXWwwFormUrlencodedRequestBodyPayload && $responsePayloadContentType === 'application/json':
                $response = $this->handler->firstOperationFromFirstOperationApplicationXWwwFormUrlencodedRequestBodyPayloadPayloadToApplicationJsonContent($pPathParamString, $pPathParamNumber, $pPathParamInteger, $pPathParamBoolean, $qQueryParamString, $qQueryParamNumber, $qQueryParamInteger, $qQueryParamBoolean, $hHeaderParamString, $hHeaderParamNumber, $hHeaderParamInteger, $hHeaderParamBoolean, $cCookieParamString, $cCookieParamNumber, $cCookieParamInteger, $cCookieParamBoolean, $requestBodyPayload);
                break;
            default:
                return new JsonResponse(['code' => 'unsupported_response_type', 'message' => "The value '{$responsePayloadContentType}' received in accept header is not a supported format."], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new \RuntimeException();
        }
    }
}