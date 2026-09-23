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
        try {
            $this->validate($pPathParamString, 'pathParamString', [new Assert\NotNull()]);
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'path', ...$error];
            }
        }
        $pPathParamNumber = $pathParamNumber;
        try {
            $this->validate($pPathParamNumber, 'pathParamNumber', [new Assert\NotNull()]);
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'path', ...$error];
            }
        }
        $pPathParamInteger = $pathParamInteger;
        try {
            $this->validate($pPathParamInteger, 'pathParamInteger', [new Assert\NotNull()]);
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'path', ...$error];
            }
        }
        $pPathParamBoolean = $pathParamBoolean;
        try {
            $this->validate($pPathParamBoolean, 'pathParamBoolean', [new Assert\NotNull()]);
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'path', ...$error];
            }
        }
        $qQueryParamString = '';
        try {
            $qQueryParamString = $this->getStringParameter($request, 'queryParamString', 'query', true);
            $this->validate($qQueryParamString, 'queryParamString', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNumber = 0.0;
        try {
            $qQueryParamNumber = $this->getFloatParameter($request, 'queryParamNumber', 'query', true);
            $this->validate($qQueryParamNumber, 'queryParamNumber', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamInteger = 0;
        try {
            $qQueryParamInteger = $this->getIntParameter($request, 'queryParamInteger', 'query', true);
            $this->validate($qQueryParamInteger, 'queryParamInteger', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamBoolean = false;
        try {
            $qQueryParamBoolean = $this->getBoolParameter($request, 'queryParamBoolean', 'query', true);
            $this->validate($qQueryParamBoolean, 'queryParamBoolean', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $hHeaderParamString = '';
        try {
            $hHeaderParamString = $this->getStringParameter($request, 'headerParamString', 'header', true);
            $this->validate($hHeaderParamString, 'headerParamString', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'header', ...$error];
            }
        }
        $hHeaderParamNumber = 0.0;
        try {
            $hHeaderParamNumber = $this->getFloatParameter($request, 'headerParamNumber', 'header', true);
            $this->validate($hHeaderParamNumber, 'headerParamNumber', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'header', ...$error];
            }
        }
        $hHeaderParamInteger = 0;
        try {
            $hHeaderParamInteger = $this->getIntParameter($request, 'headerParamInteger', 'header', true);
            $this->validate($hHeaderParamInteger, 'headerParamInteger', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'header', ...$error];
            }
        }
        $hHeaderParamBoolean = false;
        try {
            $hHeaderParamBoolean = $this->getBoolParameter($request, 'headerParamBoolean', 'header', true);
            $this->validate($hHeaderParamBoolean, 'headerParamBoolean', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'header', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'header', ...$error];
            }
        }
        $cCookieParamString = '';
        try {
            $cCookieParamString = $this->getStringParameter($request, 'cookieParamString', 'cookie', true);
            $this->validate($cCookieParamString, 'cookieParamString', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'cookie', ...$error];
            }
        }
        $cCookieParamNumber = 0.0;
        try {
            $cCookieParamNumber = $this->getFloatParameter($request, 'cookieParamNumber', 'cookie', true);
            $this->validate($cCookieParamNumber, 'cookieParamNumber', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'cookie', ...$error];
            }
        }
        $cCookieParamInteger = 0;
        try {
            $cCookieParamInteger = $this->getIntParameter($request, 'cookieParamInteger', 'cookie', true);
            $this->validate($cCookieParamInteger, 'cookieParamInteger', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'cookie', ...$error];
            }
        }
        $cCookieParamBoolean = false;
        try {
            $cCookieParamBoolean = $this->getBoolParameter($request, 'cookieParamBoolean', 'cookie', true);
            $this->validate($cCookieParamBoolean, 'cookieParamBoolean', [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'cookie', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'cookie', ...$error];
            }
        }
        $qQueryParamStringArray = [];
        try {
            $qQueryParamStringArray = $this->denormalizeQQueryParamStringArrayParameter($request, 'queryParamStringArray', 'query');
            $this->validate($qQueryParamStringArray, 'queryParamStringArray', [new Assert\NotNull(), new Assert\Count(min: 1), new Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull(), new Assert\Length(min: 2)])]);
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
            $this->validate($qQueryParamIntegerMatrix, 'queryParamIntegerMatrix', [new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: 'array'), new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: 'int'), new Assert\NotNull()])])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamAbcList = [];
        try {
            $qQueryParamAbcList = $this->denormalizeQQueryParamAbcListParameter($request, 'queryParamAbcList', 'query');
            $this->validate($qQueryParamAbcList, 'queryParamAbcList', [new Assert\NotNull(), new Assert\Valid(), new Assert\All(constraints: [new Assert\NotNull()])]);
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
            $this->validate($qQueryParamObject, 'queryParamObject', [new Assert\Valid(), new Assert\NotNull()]);
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
            $this->validate($qQueryParamAbcRef, 'queryParamAbcRef', [new Assert\Valid(), new Assert\NotNull()]);
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
            $this->validate($qQueryParamNodeTree, 'queryParamNodeTree', [new Assert\Valid(), new Assert\NotNull()]);
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
            $this->validate($qQueryParamNumberArray, 'queryParamNumberArray', [new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: ['int', 'float']), new Assert\NotNull()])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamEnumArray = [];
        try {
            $qQueryParamEnumArray = $this->denormalizeQQueryParamEnumArrayParameter($request, 'queryParamEnumArray', 'query');
            $this->validate($qQueryParamEnumArray, 'queryParamEnumArray', [new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull(), new Assert\Choice(choices: ['abc', 'def', 'ghi'])])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamRangeArray = [];
        try {
            $qQueryParamRangeArray = $this->denormalizeQQueryParamRangeArrayParameter($request, 'queryParamRangeArray', 'query');
            $this->validate($qQueryParamRangeArray, 'queryParamRangeArray', [new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: 'int'), new Assert\NotNull(), new Assert\GreaterThanOrEqual(value: 1), new Assert\LessThanOrEqual(value: 5)])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamOptionalArray = [];
        try {
            $qQueryParamOptionalArray = $this->denormalizeQQueryParamOptionalArrayParameter($request, 'queryParamOptionalArray', 'query');
            $this->validate($qQueryParamOptionalArray, 'queryParamOptionalArray', [new Assert\NotNull(), new Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull()])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNullableArray = [];
        try {
            $qQueryParamNullableArray = $this->denormalizeQQueryParamNullableArrayParameter($request, 'queryParamNullableArray', 'query');
            $this->validate($qQueryParamNullableArray, 'queryParamNullableArray', [new Assert\All(constraints: [new Assert\Type(type: 'bool'), new Assert\NotNull()])]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNullableString = '';
        try {
            $qQueryParamNullableString = $this->getStringOrNullParameter($request, 'queryParamNullableString', 'query', false, null);
            $this->validate($qQueryParamNullableString, 'queryParamNullableString', []);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNullableNumber = 0.0;
        try {
            $qQueryParamNullableNumber = $this->getFloatOrNullParameter($request, 'queryParamNullableNumber', 'query', false, null);
            $this->validate($qQueryParamNullableNumber, 'queryParamNullableNumber', []);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNullableInteger = 0;
        try {
            $qQueryParamNullableInteger = $this->getIntOrNullParameter($request, 'queryParamNullableInteger', 'query', false, null);
            $this->validate($qQueryParamNullableInteger, 'queryParamNullableInteger', []);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $qQueryParamNullableBoolean = false;
        try {
            $qQueryParamNullableBoolean = $this->getBoolOrNullParameter($request, 'queryParamNullableBoolean', 'query', false, null);
            $this->validate($qQueryParamNullableBoolean, 'queryParamNullableBoolean', []);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $requestBodyPayload = (new \ReflectionClass(Schema::class))->newInstanceWithoutConstructor();
        try {
            $requestBodyPayload = $this->denormalizeSchemaJsonValue($this->getJsonRequestBody($request), '');
            $this->validate($requestBodyPayload, '', [new Assert\Valid(), new Assert\NotNull()]);
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