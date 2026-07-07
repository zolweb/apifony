<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;
class FirstOperationController extends AbstractController
{
    private FirstOperationHandler $handler;
    public function setHandler(FirstOperationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function firstOperation(Request $request, string $pathParamString, float $pathParamNumber, int $pathParamInteger, bool $pathParamBoolean): Response
    {
        $pathErrors = [];
        $queryErrors = [];
        $headerErrors = [];
        $cookieErrors = [];
        $requestBodyErrors = [];
        $pPathParamString = $pathParamString;
        try {
            $this->validateParameter($pPathParamString, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $pathErrors['pathParamString'] = $e->messages;
        }
        $pPathParamNumber = $pathParamNumber;
        try {
            $this->validateParameter($pPathParamNumber, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $pathErrors['pathParamNumber'] = $e->messages;
        }
        $pPathParamInteger = $pathParamInteger;
        try {
            $this->validateParameter($pPathParamInteger, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $pathErrors['pathParamInteger'] = $e->messages;
        }
        $pPathParamBoolean = $pathParamBoolean;
        try {
            $this->validateParameter($pPathParamBoolean, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $pathErrors['pathParamBoolean'] = $e->messages;
        }
        $qQueryParamString = '';
        try {
            $qQueryParamString = $this->getStringParameter($request, 'queryParamString', 'query', true);
            $this->validateParameter($qQueryParamString, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamString'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamString'] = $e->messages;
        }
        $qQueryParamNumber = 0.0;
        try {
            $qQueryParamNumber = $this->getFloatParameter($request, 'queryParamNumber', 'query', true);
            $this->validateParameter($qQueryParamNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamNumber'] = $e->messages;
        }
        $qQueryParamInteger = 0;
        try {
            $qQueryParamInteger = $this->getIntParameter($request, 'queryParamInteger', 'query', true);
            $this->validateParameter($qQueryParamInteger, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamInteger'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamInteger'] = $e->messages;
        }
        $qQueryParamBoolean = false;
        try {
            $qQueryParamBoolean = $this->getBoolParameter($request, 'queryParamBoolean', 'query', true);
            $this->validateParameter($qQueryParamBoolean, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamBoolean'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamBoolean'] = $e->messages;
        }
        $hHeaderParamString = '';
        try {
            $hHeaderParamString = $this->getStringParameter($request, 'headerParamString', 'header', true);
            $this->validateParameter($hHeaderParamString, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $headerErrors['headerParamString'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $headerErrors['headerParamString'] = $e->messages;
        }
        $hHeaderParamNumber = 0.0;
        try {
            $hHeaderParamNumber = $this->getFloatParameter($request, 'headerParamNumber', 'header', true);
            $this->validateParameter($hHeaderParamNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $headerErrors['headerParamNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $headerErrors['headerParamNumber'] = $e->messages;
        }
        $hHeaderParamInteger = 0;
        try {
            $hHeaderParamInteger = $this->getIntParameter($request, 'headerParamInteger', 'header', true);
            $this->validateParameter($hHeaderParamInteger, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $headerErrors['headerParamInteger'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $headerErrors['headerParamInteger'] = $e->messages;
        }
        $hHeaderParamBoolean = false;
        try {
            $hHeaderParamBoolean = $this->getBoolParameter($request, 'headerParamBoolean', 'header', true);
            $this->validateParameter($hHeaderParamBoolean, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $headerErrors['headerParamBoolean'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $headerErrors['headerParamBoolean'] = $e->messages;
        }
        $cCookieParamString = '';
        try {
            $cCookieParamString = $this->getStringParameter($request, 'cookieParamString', 'cookie', true);
            $this->validateParameter($cCookieParamString, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $cookieErrors['cookieParamString'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $cookieErrors['cookieParamString'] = $e->messages;
        }
        $cCookieParamNumber = 0.0;
        try {
            $cCookieParamNumber = $this->getFloatParameter($request, 'cookieParamNumber', 'cookie', true);
            $this->validateParameter($cCookieParamNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $cookieErrors['cookieParamNumber'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $cookieErrors['cookieParamNumber'] = $e->messages;
        }
        $cCookieParamInteger = 0;
        try {
            $cCookieParamInteger = $this->getIntParameter($request, 'cookieParamInteger', 'cookie', true);
            $this->validateParameter($cCookieParamInteger, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $cookieErrors['cookieParamInteger'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $cookieErrors['cookieParamInteger'] = $e->messages;
        }
        $cCookieParamBoolean = false;
        try {
            $cCookieParamBoolean = $this->getBoolParameter($request, 'cookieParamBoolean', 'cookie', true);
            $this->validateParameter($cCookieParamBoolean, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $cookieErrors['cookieParamBoolean'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $cookieErrors['cookieParamBoolean'] = $e->messages;
        }
        $requestBodyPayload = (new \ReflectionClass(Schema::class))->newInstanceWithoutConstructor();
        try {
            $requestBodyPayload = $this->getObjectRequestBody($request, Schema::class);
            $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $requestBodyErrors = [$e->getMessage()];
        } catch (RequestBodyValidationException $e) {
            $requestBodyErrors = $e->messages;
        }
        $errors = [];
        if (\count($pathErrors) > 0) {
            $errors['path'] = $pathErrors;
        }
        if (\count($queryErrors) > 0) {
            $errors['query'] = $queryErrors;
        }
        if (\count($headerErrors) > 0) {
            $errors['header'] = $headerErrors;
        }
        if (\count($cookieErrors) > 0) {
            $errors['cookie'] = $cookieErrors;
        }
        if (\count($requestBodyErrors) > 0) {
            $errors['requestBody'] = $requestBodyErrors;
        }
        if (\count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $response = $this->handler->firstOperation($pPathParamString, $pPathParamNumber, $pPathParamInteger, $pPathParamBoolean, $qQueryParamString, $qQueryParamNumber, $qQueryParamInteger, $qQueryParamBoolean, $hHeaderParamString, $hHeaderParamNumber, $hHeaderParamInteger, $hHeaderParamBoolean, $cCookieParamString, $cCookieParamNumber, $cCookieParamInteger, $cCookieParamBoolean, $requestBodyPayload);
        if ($response->getContentType() === 'application/json') {
            return new JsonResponse($response->payload, $response->getCode(), $response->getHeaders());
        }
        return new Response('', $response->getCode(), $response->getHeaders());
    }
}