<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\Tag;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;
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
        $requestBodyPayload = (new \ReflectionClass(Schema::class))->newInstanceWithoutConstructor();
        try {
            $requestBodyPayload = $this->getObjectRequestBody($request, Schema::class);
            $this->validateRequestBody($requestBodyPayload, [new Assert\Valid(), new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['requestBody'] = [$e->getMessage()];
        } catch (RequestBodyValidationException $e) {
            $errors['requestBody'] = $e->messages;
        }
        if (\count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $response = $this->handler->firstOperation($pPathParamString, $pPathParamNumber, $pPathParamInteger, $pPathParamBoolean, $qQueryParamString, $qQueryParamNumber, $qQueryParamInteger, $qQueryParamBoolean, $hHeaderParamString, $hHeaderParamNumber, $hHeaderParamInteger, $hHeaderParamBoolean, $cCookieParamString, $cCookieParamNumber, $cCookieParamInteger, $cCookieParamBoolean, $requestBodyPayload);
        if ($response::CONTENT_TYPE === 'application/json') {
            return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
        }
        return new Response('', $response::CODE, $response->getHeaders());
    }
}