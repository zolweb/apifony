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
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Node;
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
        $qQueryParamStringArray = [];
        try {
            $qQueryParamStringArray = $this->denormalizeQQueryParamStringArrayParameter($request, 'queryParamStringArray', 'query');
            $this->validateParameter($qQueryParamStringArray, [new Assert\NotNull(), new Assert\Count(min: 1), new Assert\All(constraints: [new Assert\NotNull(), new Assert\Length(min: 2)])]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamStringArray'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamStringArray'] = $e->messages;
        }
        $qQueryParamIntegerMatrix = [];
        try {
            $qQueryParamIntegerMatrix = $this->denormalizeQQueryParamIntegerMatrixParameter($request, 'queryParamIntegerMatrix', 'query');
            $this->validateParameter($qQueryParamIntegerMatrix, [new Assert\NotNull(), new Assert\All(constraints: [new Assert\NotNull(), new Assert\All(constraints: [new Assert\NotNull()])])]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamIntegerMatrix'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamIntegerMatrix'] = $e->messages;
        }
        $qQueryParamAbcList = [];
        try {
            $qQueryParamAbcList = $this->denormalizeQQueryParamAbcListParameter($request, 'queryParamAbcList', 'query');
            $this->validateParameter($qQueryParamAbcList, [new Assert\NotNull(), new Assert\Valid(), new Assert\All(constraints: [new Assert\NotNull()])]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamAbcList'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamAbcList'] = $e->messages;
        }
        $qQueryParamObject = (new \ReflectionClass(FirstOperationQueryParamObject::class))->newInstanceWithoutConstructor();
        try {
            $qQueryParamObject = $this->denormalizeQQueryParamObjectParameter($request, 'queryParamObject', 'query');
            $this->validateParameter($qQueryParamObject, [new Assert\Valid(), new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamObject'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamObject'] = $e->messages;
        }
        $qQueryParamAbcRef = (new \ReflectionClass(Abc::class))->newInstanceWithoutConstructor();
        try {
            $qQueryParamAbcRef = $this->denormalizeQQueryParamAbcRefParameter($request, 'queryParamAbcRef', 'query');
            $this->validateParameter($qQueryParamAbcRef, [new Assert\Valid(), new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamAbcRef'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamAbcRef'] = $e->messages;
        }
        $qQueryParamNodeTree = (new \ReflectionClass(Node::class))->newInstanceWithoutConstructor();
        try {
            $qQueryParamNodeTree = $this->denormalizeQQueryParamNodeTreeParameter($request, 'queryParamNodeTree', 'query');
            $this->validateParameter($qQueryParamNodeTree, [new Assert\Valid(), new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamNodeTree'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamNodeTree'] = $e->messages;
        }
        $qQueryParamOptionalArray = [];
        try {
            $qQueryParamOptionalArray = $this->denormalizeQQueryParamOptionalArrayParameter($request, 'queryParamOptionalArray', 'query');
            $this->validateParameter($qQueryParamOptionalArray, [new Assert\NotNull(), new Assert\All(constraints: [new Assert\NotNull()])]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamOptionalArray'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamOptionalArray'] = $e->messages;
        }
        $qQueryParamNullableArray = [];
        try {
            $qQueryParamNullableArray = $this->denormalizeQQueryParamNullableArrayParameter($request, 'queryParamNullableArray', 'query');
            $this->validateParameter($qQueryParamNullableArray, [new Assert\All(constraints: [new Assert\NotNull()])]);
        } catch (DenormalizationException $e) {
            $queryErrors['queryParamNullableArray'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['queryParamNullableArray'] = $e->messages;
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
        $response = $this->handler->firstOperation($pPathParamString, $pPathParamNumber, $pPathParamInteger, $pPathParamBoolean, $qQueryParamString, $qQueryParamNumber, $qQueryParamInteger, $qQueryParamBoolean, $hHeaderParamString, $hHeaderParamNumber, $hHeaderParamInteger, $hHeaderParamBoolean, $cCookieParamString, $cCookieParamNumber, $cCookieParamInteger, $cCookieParamBoolean, $qQueryParamStringArray, $qQueryParamIntegerMatrix, $qQueryParamAbcList, $qQueryParamObject, $qQueryParamAbcRef, $qQueryParamNodeTree, $qQueryParamOptionalArray, $qQueryParamNullableArray, $requestBodyPayload);
        if ($response->getContentType() === 'application/json') {
            return new JsonResponse($response->payload, $response->getCode(), $response->getHeaders());
        }
        return new Response('', $response->getCode(), $response->getHeaders());
    }
    /**
     * @return list<string>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamStringArrayParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name, $in) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeStringParameter($v2, $v3, $in);
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @return list<list<int<min,max>>>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamIntegerMatrixParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name, $in) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = [];
            foreach ($this->denormalizeListParameter($v2, $v3, $in) as $v5 => $v6) {
                $v7 = "{$v3}[{$v5}]";
                $v8 = $this->denormalizeIntParameter($v6, $v7, $in);
                $v4[] = $v8;
            }
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @return list<Abc>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamAbcListParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name, $in) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeAbcParameterValue($v2, $v3, $in);
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamObjectParameter(Request $request, string $name, string $in): FirstOperationQueryParamObject
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        return $this->denormalizeFirstOperationQueryParamObjectParameterValue($this->getRawParameter($request, $name, $in), $name, $in);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamAbcRefParameter(Request $request, string $name, string $in): Abc
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        return $this->denormalizeAbcParameterValue($this->getRawParameter($request, $name, $in), $name, $in);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamNodeTreeParameter(Request $request, string $name, string $in): Node
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        return $this->denormalizeNodeParameterValue($this->getRawParameter($request, $name, $in), $name, $in);
    }
    /**
     * @return list<string>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamOptionalArrayParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            return [];
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name, $in) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeStringParameter($v2, $v3, $in);
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @return ?list<bool>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamNullableArrayParameter(Request $request, string $name, string $in): ?array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            return null;
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name, $in) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeBoolParameter($v2, $v3, $in);
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeAbcParameterValue(mixed $value, string $path, string $in): Abc
    {
        $v0 = $this->denormalizeMapParameter($value, $path, $in);
        $v1 = "{$path}[def]";
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'def', $v1, $in), $v1, $in);
        return new Abc(def: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeFirstOperationQueryParamObjectParameterValue(mixed $value, string $path, string $in): FirstOperationQueryParamObject
    {
        $v0 = $this->denormalizeMapParameter($value, $path, $in);
        $v1 = "{$path}[stringProperty]";
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'stringProperty', $v1, $in), $v1, $in);
        $v3 = "{$path}[nestedArrayProperty]";
        $v4 = [];
        foreach ($this->denormalizeListParameter($this->getRequiredParameterProperty($v0, 'nestedArrayProperty', $v3, $in), $v3, $in) as $v5 => $v6) {
            $v7 = "{$v3}[{$v5}]";
            $v8 = $this->denormalizeIntParameter($v6, $v7, $in);
            $v4[] = $v8;
        }
        $v9 = "{$path}[nestedObjectProperty]";
        $v10 = $this->denormalizeFirstOperationQueryParamObjectNestedObjectPropertyParameterValue($this->getRequiredParameterProperty($v0, 'nestedObjectProperty', $v9, $in), $v9, $in);
        $v12 = 'abc';
        if (\array_key_exists('optionalProperty', $v0)) {
            $v11 = "{$path}[optionalProperty]";
            $v12 = $this->denormalizeStringParameter($v0['optionalProperty'], $v11, $in);
        }
        return new FirstOperationQueryParamObject(stringProperty: $v2, nestedArrayProperty: $v4, nestedObjectProperty: $v10, optionalProperty: $v12);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeNodeParameterValue(mixed $value, string $path, string $in): Node
    {
        $v0 = $this->denormalizeMapParameter($value, $path, $in);
        $v1 = "{$path}[name]";
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'name', $v1, $in), $v1, $in);
        $v4 = [];
        if (\array_key_exists('children', $v0)) {
            $v3 = "{$path}[children]";
            $v4 = [];
            foreach ($this->denormalizeListParameter($v0['children'], $v3, $in) as $v5 => $v6) {
                $v7 = "{$v3}[{$v5}]";
                $v8 = $this->denormalizeNodeParameterValue($v6, $v7, $in);
                $v4[] = $v8;
            }
        }
        return new Node(name: $v2, children: $v4);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeFirstOperationQueryParamObjectNestedObjectPropertyParameterValue(mixed $value, string $path, string $in): FirstOperationQueryParamObjectNestedObjectProperty
    {
        $v0 = $this->denormalizeMapParameter($value, $path, $in);
        $v1 = "{$path}[emailProperty]";
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'emailProperty', $v1, $in), $v1, $in);
        return new FirstOperationQueryParamObjectNestedObjectProperty(emailProperty: $v2);
    }
}