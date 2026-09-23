<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ValidationException;
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
        $errors = [];
        $pPathParamString = $pathParamString;
        $pPathParamNumber = $pathParamNumber;
        $pPathParamInteger = $pathParamInteger;
        $pPathParamBoolean = $pathParamBoolean;
        $qQueryParamString = '';
        try {
            $qQueryParamString = $this->getStringParameter($request, 'queryParamString', 'query', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamNumber = 0.0;
        try {
            $qQueryParamNumber = $this->getFloatParameter($request, 'queryParamNumber', 'query', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamInteger = 0;
        try {
            $qQueryParamInteger = $this->getIntParameter($request, 'queryParamInteger', 'query', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamBoolean = false;
        try {
            $qQueryParamBoolean = $this->getBoolParameter($request, 'queryParamBoolean', 'query', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $hHeaderParamString = '';
        try {
            $hHeaderParamString = $this->getStringParameter($request, 'headerParamString', 'header', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $hHeaderParamNumber = 0.0;
        try {
            $hHeaderParamNumber = $this->getFloatParameter($request, 'headerParamNumber', 'header', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $hHeaderParamInteger = 0;
        try {
            $hHeaderParamInteger = $this->getIntParameter($request, 'headerParamInteger', 'header', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $hHeaderParamBoolean = false;
        try {
            $hHeaderParamBoolean = $this->getBoolParameter($request, 'headerParamBoolean', 'header', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $cCookieParamString = '';
        try {
            $cCookieParamString = $this->getStringParameter($request, 'cookieParamString', 'cookie', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $cCookieParamNumber = 0.0;
        try {
            $cCookieParamNumber = $this->getFloatParameter($request, 'cookieParamNumber', 'cookie', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $cCookieParamInteger = 0;
        try {
            $cCookieParamInteger = $this->getIntParameter($request, 'cookieParamInteger', 'cookie', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $cCookieParamBoolean = false;
        try {
            $cCookieParamBoolean = $this->getBoolParameter($request, 'cookieParamBoolean', 'cookie', true);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamStringArray = [];
        try {
            $qQueryParamStringArray = $this->denormalizeQQueryParamStringArrayParameter($request, 'queryParamStringArray', 'query');
            $this->validate($qQueryParamStringArray, 'queryParamStringArray', [new Assert\Count(min: 1), new Assert\All(constraints: [new Assert\Length(min: 2)])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamIntegerMatrix = [];
        try {
            $qQueryParamIntegerMatrix = $this->denormalizeQQueryParamIntegerMatrixParameter($request, 'queryParamIntegerMatrix', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamAbcList = [];
        try {
            $qQueryParamAbcList = $this->denormalizeQQueryParamAbcListParameter($request, 'queryParamAbcList', 'query');
            $this->validate($qQueryParamAbcList, 'queryParamAbcList', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamObject = (new \ReflectionClass(FirstOperationQueryParamObject::class))->newInstanceWithoutConstructor();
        try {
            $qQueryParamObject = $this->denormalizeQQueryParamObjectParameter($request, 'queryParamObject', 'query');
            $this->validate($qQueryParamObject, 'queryParamObject', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamAbcRef = (new \ReflectionClass(Abc::class))->newInstanceWithoutConstructor();
        try {
            $qQueryParamAbcRef = $this->denormalizeQQueryParamAbcRefParameter($request, 'queryParamAbcRef', 'query');
            $this->validate($qQueryParamAbcRef, 'queryParamAbcRef', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNodeTree = (new \ReflectionClass(Node::class))->newInstanceWithoutConstructor();
        try {
            $qQueryParamNodeTree = $this->denormalizeQQueryParamNodeTreeParameter($request, 'queryParamNodeTree', 'query');
            $this->validate($qQueryParamNodeTree, 'queryParamNodeTree', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNumberArray = [];
        try {
            $qQueryParamNumberArray = $this->denormalizeQQueryParamNumberArrayParameter($request, 'queryParamNumberArray', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamEnumArray = [];
        try {
            $qQueryParamEnumArray = $this->denormalizeQQueryParamEnumArrayParameter($request, 'queryParamEnumArray', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamRangeArray = [];
        try {
            $qQueryParamRangeArray = $this->denormalizeQQueryParamRangeArrayParameter($request, 'queryParamRangeArray', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamOptionalArray = [];
        try {
            $qQueryParamOptionalArray = $this->denormalizeQQueryParamOptionalArrayParameter($request, 'queryParamOptionalArray', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamNullableArray = [];
        try {
            $qQueryParamNullableArray = $this->denormalizeQQueryParamNullableArrayParameter($request, 'queryParamNullableArray', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamNullableString = '';
        try {
            $qQueryParamNullableString = $this->getStringOrNullParameter($request, 'queryParamNullableString', 'query', false, null);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamNullableNumber = 0.0;
        try {
            $qQueryParamNullableNumber = $this->getFloatOrNullParameter($request, 'queryParamNullableNumber', 'query', false, null);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamNullableInteger = 0;
        try {
            $qQueryParamNullableInteger = $this->getIntOrNullParameter($request, 'queryParamNullableInteger', 'query', false, null);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qQueryParamNullableBoolean = false;
        try {
            $qQueryParamNullableBoolean = $this->getBoolOrNullParameter($request, 'queryParamNullableBoolean', 'query', false, null);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $requestBodyPayload = (new \ReflectionClass(Schema::class))->newInstanceWithoutConstructor();
        try {
            $requestBodyPayload = $this->denormalizeSchemaJsonValue($this->getJsonRequestBody($request), '');
            $this->validate($requestBodyPayload, '', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'requestBody', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'requestBody', ...$error];
            }
        }
        if (\count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $response = $this->handler->firstOperation($pPathParamString, $pPathParamNumber, $pPathParamInteger, $pPathParamBoolean, $qQueryParamString, $qQueryParamNumber, $qQueryParamInteger, $qQueryParamBoolean, $hHeaderParamString, $hHeaderParamNumber, $hHeaderParamInteger, $hHeaderParamBoolean, $cCookieParamString, $cCookieParamNumber, $cCookieParamInteger, $cCookieParamBoolean, $qQueryParamStringArray, $qQueryParamIntegerMatrix, $qQueryParamAbcList, $qQueryParamObject, $qQueryParamAbcRef, $qQueryParamNodeTree, $qQueryParamNumberArray, $qQueryParamEnumArray, $qQueryParamRangeArray, $qQueryParamOptionalArray, $qQueryParamNullableArray, $qQueryParamNullableString, $qQueryParamNullableNumber, $qQueryParamNullableInteger, $qQueryParamNullableBoolean, $requestBodyPayload);
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
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeStringParameter($v2, $v3);
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
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = [];
            foreach ($this->denormalizeListParameter($v2, $v3) as $v5 => $v6) {
                $v7 = "{$v3}[{$v5}]";
                $v8 = $this->denormalizeIntParameter($v6, $v7);
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
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeAbcParameterValue($v2, $v3);
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
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        return $this->denormalizeFirstOperationQueryParamObjectParameterValue($this->getRawParameter($request, $name, $in), $name);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamAbcRefParameter(Request $request, string $name, string $in): Abc
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        return $this->denormalizeAbcParameterValue($this->getRawParameter($request, $name, $in), $name);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamNodeTreeParameter(Request $request, string $name, string $in): Node
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        return $this->denormalizeNodeParameterValue($this->getRawParameter($request, $name, $in), $name);
    }
    /**
     * @return list<float>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamNumberArrayParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeFloatParameter($v2, $v3);
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @return list<'abc'|'def'|'ghi'>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamEnumArrayParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeStringParameter($v2, $v3);
            if (!\in_array($v4, ['abc', 'def', 'ghi'], true)) {
                throw new DenormalizationException($v3, 'invalid_enum_value', 'This value should be one of \'abc\', \'def\', \'ghi\'.');
            }
            $v0[] = $v4;
        }
        return $v0;
    }
    /**
     * @return list<int<1,5>>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQQueryParamRangeArrayParameter(Request $request, string $name, string $in): array
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        $v0 = [];
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeIntParameter($v2, $v3);
            if ($v4 < 1 || $v4 > 5) {
                throw new DenormalizationException($v3, 'out_of_range', 'This value should be between 1 and 5.');
            }
            $v0[] = $v4;
        }
        return $v0;
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
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeStringParameter($v2, $v3);
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
        foreach ($this->denormalizeListParameter($this->getRawParameter($request, $name, $in), $name) as $v1 => $v2) {
            $v3 = "{$name}[{$v1}]";
            $v4 = $this->denormalizeBoolParameter($v2, $v3);
            $v0[] = $v4;
        }
        return $v0;
    }
}