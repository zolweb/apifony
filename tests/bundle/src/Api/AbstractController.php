<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
use Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation\FirstOperationQueryParamObject;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Node;
use Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation\FirstOperationQueryParamObjectNestedObjectProperty;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation\RawShapesOperationRequestBodyPayload;
use Zol\Apifony\Tests\TestOpenApiServer\Model\SchemaObjectProperty;
use Zol\Apifony\Tests\TestOpenApiServer\Model\SchemaObjectArrayProperty;
abstract class AbstractController
{
    public function __construct(protected readonly ValidatorInterface $validator)
    {
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringParameter(Request $request, string $name, string $in, bool $required, ?string $default = null): string
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            if ($default === null) {
                throw new DenormalizationException($name, 'required', 'This value should not be null.');
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException($name, 'required', 'This value should not be null.');
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringOrNullParameter(Request $request, string $name, string $in, bool $required, ?string $default = null): ?string
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntParameter(Request $request, string $name, string $in, bool $required, ?int $default = null): int
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            if ($default === null) {
                throw new DenormalizationException($name, 'required', 'This value should not be null.');
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException($name, 'required', 'This value should not be null.');
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type integer.');
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type integer.');
        }
        return (int) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntOrNullParameter(Request $request, string $name, string $in, bool $required, ?int $default = null): ?int
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type integer.');
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type integer.');
        }
        return (int) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatParameter(Request $request, string $name, string $in, bool $required, ?float $default = null): float
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            if ($default === null) {
                throw new DenormalizationException($name, 'required', 'This value should not be null.');
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException($name, 'required', 'This value should not be null.');
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type number.');
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type number.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatOrNullParameter(Request $request, string $name, string $in, bool $required, ?float $default = null): ?float
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type number.');
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type number.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolParameter(Request $request, string $name, string $in, bool $required, ?bool $default = null): bool
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            if ($default === null) {
                throw new DenormalizationException($name, 'required', 'This value should not be null.');
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException($name, 'required', 'This value should not be null.');
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type boolean.');
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type boolean.');
        }
        return ['true' => true, 'false' => false][$value];
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolOrNullParameter(Request $request, string $name, string $in, bool $required, ?bool $default = null): ?bool
    {
        $isset = $this->hasParameter($request, $name, $in);
        $value = $this->getRawParameter($request, $name, $in);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException($name, 'required', 'This value is required.');
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type boolean.');
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException($name, 'invalid_type', 'This value should be of type boolean.');
        }
        return ['true' => true, 'false' => false][$value];
    }
    public function hasParameter(Request $request, string $name, string $in): bool
    {
        return match ($in) {
            'query' => \array_key_exists($name, $request->query->all()),
            'header' => $request->headers->has($name),
            'cookie' => \array_key_exists($name, $request->cookies->all()),
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
    }
    /**
     * Reads a parameter without any type constraint. Query and cookie values are read through the
     * whole bag, as InputBag::get() rejects non scalar values with a BadRequestException that would
     * escape the validation_failed envelope.
     */
    public function getRawParameter(Request $request, string $name, string $in): mixed
    {
        return match ($in) {
            'query' => $request->query->all()[$name] ?? null,
            'header' => $request->headers->get($name),
            'cookie' => $request->cookies->all()[$name] ?? null,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
    }
    /**
     * Query strings can only express a list with empty brackets, as in 'tags[]=a&tags[]=b'.
     * Anything that would need its keys to be rewritten is rejected rather than reindexed.
     *
     * @return list<mixed>
     *
     * @throws DenormalizationException
     */
    public function denormalizeListParameter(mixed $value, string $path): array
    {
        if (!\is_array($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be an array.');
        }
        if (!array_is_list($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be a list.');
        }
        return $value;
    }
    /**
     * @return array<string, mixed>
     *
     * @throws DenormalizationException
     */
    public function denormalizeMapParameter(mixed $value, string $path): array
    {
        if (!\is_array($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be an object.');
        }
        $values = [];
        foreach ($value as $key => $item) {
            $values[(string) $key] = $item;
        }
        return $values;
    }
    /**
     * @param array<string, mixed> $values
     *
     * @throws DenormalizationException
     */
    public function getRequiredParameterProperty(array $values, string $key, string $path): mixed
    {
        if (!\array_key_exists($key, $values)) {
            throw new DenormalizationException($path, 'required', 'This value is required.');
        }
        return $values[$key];
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeStringParameter(mixed $value, string $path): string
    {
        if (!\is_string($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeIntParameter(mixed $value, string $path): int
    {
        if (!\is_string($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type integer.');
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type integer.');
        }
        return (int) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFloatParameter(mixed $value, string $path): float
    {
        if (!\is_string($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type number.');
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type number.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeBoolParameter(mixed $value, string $path): bool
    {
        if (!\is_string($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type boolean.');
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type boolean.');
        }
        return ['true' => true, 'false' => false][$value];
    }
    /**
     * @throws DenormalizationException
     */
    public function getJsonRequestBody(Request $request): mixed
    {
        $value = $request->getContent();
        if ($value === '') {
            throw new DenormalizationException('', 'required', 'This value is required.');
        }
        $value = json_decode($value, true);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new DenormalizationException('', 'invalid_json', 'The request body is not a valid JSON document.');
        }
        return $value;
    }
    /**
     * @return list<mixed>
     *
     * @throws DenormalizationException
     */
    public function denormalizeListJson(mixed $value, string $path): array
    {
        if (!\is_array($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be an array.');
        }
        if (!array_is_list($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be a list.');
        }
        return $value;
    }
    /**
     * @return array<string, mixed>
     *
     * @throws DenormalizationException
     */
    public function denormalizeMapJson(mixed $value, string $path): array
    {
        if (!\is_array($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be an object.');
        }
        $values = [];
        foreach ($value as $key => $item) {
            $values[(string) $key] = $item;
        }
        return $values;
    }
    /**
     * @param array<string, mixed> $values
     *
     * @throws DenormalizationException
     */
    public function getRequiredJsonProperty(array $values, string $key, string $path): mixed
    {
        if (!\array_key_exists($key, $values)) {
            throw new DenormalizationException($path, 'required', 'This value is required.');
        }
        return $values[$key];
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeStringJson(mixed $value, string $path): string
    {
        if (!\is_string($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeIntJson(mixed $value, string $path): int
    {
        if (!\is_int($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type integer.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFloatJson(mixed $value, string $path): float
    {
        if (!\is_int($value) && !\is_float($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type number.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeBoolJson(mixed $value, string $path): bool
    {
        if (!\is_bool($value)) {
            throw new DenormalizationException($path, 'invalid_type', 'This value should be of type boolean.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeAbcParameterValue(mixed $value, string $path): Abc
    {
        $v0 = $this->denormalizeMapParameter($value, $path);
        $v1 = $this->appendPath($path, 'def');
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'def', $v1), $v1);
        return new Abc(def: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFirstOperationQueryParamObjectParameterValue(mixed $value, string $path): FirstOperationQueryParamObject
    {
        $v0 = $this->denormalizeMapParameter($value, $path);
        $v1 = $this->appendPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'stringProperty', $v1), $v1);
        $v3 = $this->appendPath($path, 'nestedArrayProperty');
        $v4 = [];
        foreach ($this->denormalizeListParameter($this->getRequiredParameterProperty($v0, 'nestedArrayProperty', $v3), $v3) as $v5 => $v6) {
            $v7 = "{$v3}[{$v5}]";
            $v8 = $this->denormalizeIntParameter($v6, $v7);
            $v4[] = $v8;
        }
        $v9 = $this->appendPath($path, 'nestedObjectProperty');
        $v10 = $this->denormalizeFirstOperationQueryParamObjectNestedObjectPropertyParameterValue($this->getRequiredParameterProperty($v0, 'nestedObjectProperty', $v9), $v9);
        $v12 = 'abc';
        if (\array_key_exists('optionalProperty', $v0)) {
            $v11 = $this->appendPath($path, 'optionalProperty');
            $v12 = $this->denormalizeStringParameter($v0['optionalProperty'], $v11);
        }
        return new FirstOperationQueryParamObject(stringProperty: $v2, nestedArrayProperty: $v4, nestedObjectProperty: $v10, optionalProperty: $v12);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeNodeParameterValue(mixed $value, string $path): Node
    {
        $v0 = $this->denormalizeMapParameter($value, $path);
        $v1 = $this->appendPath($path, 'name');
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'name', $v1), $v1);
        $v4 = [];
        if (\array_key_exists('children', $v0)) {
            $v3 = $this->appendPath($path, 'children');
            $v4 = [];
            foreach ($this->denormalizeListParameter($v0['children'], $v3) as $v5 => $v6) {
                $v7 = "{$v3}[{$v5}]";
                $v8 = $this->denormalizeNodeParameterValue($v6, $v7);
                $v4[] = $v8;
            }
        }
        return new Node(name: $v2, children: $v4);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFirstOperationQueryParamObjectNestedObjectPropertyParameterValue(mixed $value, string $path): FirstOperationQueryParamObjectNestedObjectProperty
    {
        $v0 = $this->denormalizeMapParameter($value, $path);
        $v1 = $this->appendPath($path, 'emailProperty');
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'emailProperty', $v1), $v1);
        return new FirstOperationQueryParamObjectNestedObjectProperty(emailProperty: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeSchemaJsonValue(mixed $value, string $path): Schema
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'stringProperty', $v1), $v1);
        $v3 = $this->appendPath($path, 'numberProperty');
        $v4 = $this->denormalizeFloatJson($this->getRequiredJsonProperty($v0, 'numberProperty', $v3), $v3);
        $v5 = $this->appendPath($path, 'integerProperty');
        $v6 = $this->denormalizeIntJson($this->getRequiredJsonProperty($v0, 'integerProperty', $v5), $v5);
        $v7 = $this->appendPath($path, 'booleanProperty');
        $v8 = $this->denormalizeBoolJson($this->getRequiredJsonProperty($v0, 'booleanProperty', $v7), $v7);
        $v9 = $this->appendPath($path, 'enumStringProperty');
        $v10 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'enumStringProperty', $v9), $v9);
        if (!\in_array($v10, ['abc', 'def', 'ghi'], true)) {
            throw new DenormalizationException($v9, 'invalid_enum_value', 'This value should be one of \'abc\', \'def\', \'ghi\'.');
        }
        $v11 = $this->appendPath($path, 'enumNullableStringProperty');
        $v13 = $this->getRequiredJsonProperty($v0, 'enumNullableStringProperty', $v11);
        $v12 = null;
        if ($v13 !== null) {
            $v12 = $this->denormalizeStringJson($v13, $v11);
            if (!\in_array($v12, ['abc', 'def', 'ghi', null], true)) {
                throw new DenormalizationException($v11, 'invalid_enum_value', 'This value should be one of \'abc\', \'def\', \'ghi\', null.');
            }
        }
        $v14 = $this->appendPath($path, 'integerRangeProperty');
        $v15 = $this->denormalizeIntJson($this->getRequiredJsonProperty($v0, 'integerRangeProperty', $v14), $v14);
        if ($v15 < -5 || $v15 > 5) {
            throw new DenormalizationException($v14, 'out_of_range', 'This value should be between -5 and 5.');
        }
        $v16 = $this->appendPath($path, 'emailProperty');
        $v17 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'emailProperty', $v16), $v16);
        $v18 = $this->appendPath($path, 'uuidProperty');
        $v19 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'uuidProperty', $v18), $v18);
        $v20 = $this->appendPath($path, 'dateTimeProperty');
        $v21 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty', $v20), $v20);
        $v22 = $this->appendPath($path, 'dateTimeProperty2');
        $v23 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty2', $v22), $v22);
        $v24 = $this->appendPath($path, 'dateTimeProperty3');
        $v25 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty3', $v24), $v24);
        $v26 = $this->appendPath($path, 'dateTimeProperty4');
        $v27 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty4', $v26), $v26);
        $v28 = $this->appendPath($path, 'dateProperty');
        $v29 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateProperty', $v28), $v28);
        $v30 = $this->appendPath($path, 'timeProperty');
        $v31 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty', $v30), $v30);
        $v32 = $this->appendPath($path, 'timeProperty2');
        $v33 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty2', $v32), $v32);
        $v34 = $this->appendPath($path, 'timeProperty3');
        $v35 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty3', $v34), $v34);
        $v36 = $this->appendPath($path, 'timeProperty4');
        $v37 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty4', $v36), $v36);
        $v38 = $this->appendPath($path, 'customProperty');
        $v39 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'customProperty', $v38), $v38);
        $v41 = 'abc';
        if (\array_key_exists('defaultProperty', $v0)) {
            $v40 = $this->appendPath($path, 'defaultProperty');
            $v41 = $this->denormalizeStringJson($v0['defaultProperty'], $v40);
        }
        $v43 = null;
        if (\array_key_exists('nullDefaultProperty', $v0)) {
            $v42 = $this->appendPath($path, 'nullDefaultProperty');
            $v44 = $v0['nullDefaultProperty'];
            $v43 = null;
            if ($v44 !== null) {
                $v43 = $this->denormalizeStringJson($v44, $v42);
            }
        }
        $v46 = [];
        if (\array_key_exists('emptyArrayDefaultProperty', $v0)) {
            $v45 = $this->appendPath($path, 'emptyArrayDefaultProperty');
            $v46 = [];
            foreach ($this->denormalizeListJson($v0['emptyArrayDefaultProperty'], $v45) as $v47 => $v48) {
                $v49 = "{$v45}[{$v47}]";
                $v50 = $this->denormalizeStringJson($v48, $v49);
                $v46[] = $v50;
            }
        }
        $v52 = 'def';
        if (\array_key_exists('overriddenProperty', $v0)) {
            $v51 = $this->appendPath($path, 'overriddenProperty');
            $v52 = $this->denormalizeStringJson($v0['overriddenProperty'], $v51);
        }
        $v53 = $this->appendPath($path, 'objectProperty');
        $v54 = $this->denormalizeSchemaObjectPropertyJsonValue($this->getRequiredJsonProperty($v0, 'objectProperty', $v53), $v53);
        $v55 = $this->appendPath($path, 'arrayProperty');
        $v56 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'arrayProperty', $v55), $v55) as $v57 => $v58) {
            $v59 = "{$v55}[{$v57}]";
            $v60 = $this->denormalizeStringJson($v58, $v59);
            $v56[] = $v60;
        }
        $v61 = $this->appendPath($path, 'rawProperty');
        $v62 = $this->getRequiredJsonProperty($v0, 'rawProperty', $v61);
        $v63 = $this->appendPath($path, 'integerMatrixProperty');
        $v64 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'integerMatrixProperty', $v63), $v63) as $v65 => $v66) {
            $v67 = "{$v63}[{$v65}]";
            $v68 = [];
            foreach ($this->denormalizeListJson($v66, $v67) as $v69 => $v70) {
                $v71 = "{$v67}[{$v69}]";
                $v72 = $this->denormalizeIntJson($v70, $v71);
                $v68[] = $v72;
            }
            $v64[] = $v68;
        }
        $v73 = $this->appendPath($path, 'objectArrayProperty');
        $v74 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'objectArrayProperty', $v73), $v73) as $v75 => $v76) {
            $v77 = "{$v73}[{$v75}]";
            $v78 = $this->denormalizeSchemaObjectArrayPropertyJsonValue($v76, $v77);
            $v74[] = $v78;
        }
        $v79 = $this->appendPath($path, 'recursiveObjectArray');
        $v80 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'recursiveObjectArray', $v79), $v79) as $v81 => $v82) {
            $v83 = "{$v79}[{$v81}]";
            $v84 = $this->denormalizeSchemaJsonValue($v82, $v83);
            $v80[] = $v84;
        }
        return new Schema(stringProperty: $v2, numberProperty: $v4, integerProperty: $v6, booleanProperty: $v8, enumStringProperty: $v10, enumNullableStringProperty: $v12, integerRangeProperty: $v15, emailProperty: $v17, uuidProperty: $v19, dateTimeProperty: $v21, dateTimeProperty2: $v23, dateTimeProperty3: $v25, dateTimeProperty4: $v27, dateProperty: $v29, timeProperty: $v31, timeProperty2: $v33, timeProperty3: $v35, timeProperty4: $v37, customProperty: $v39, defaultProperty: $v41, nullDefaultProperty: $v43, emptyArrayDefaultProperty: $v46, overriddenProperty: $v52, objectProperty: $v54, arrayProperty: $v56, rawProperty: $v62, integerMatrixProperty: $v64, objectArrayProperty: $v74, recursiveObjectArray: $v80);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeRawShapesOperationRequestBodyPayloadJsonValue(mixed $value, string $path): RawShapesOperationRequestBodyPayload
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendPath($path, 'nullableRaw');
        $v2 = $this->getRequiredJsonProperty($v0, 'nullableRaw', $v1);
        $v3 = $this->appendPath($path, 'refRaw');
        $v4 = $this->getRequiredJsonProperty($v0, 'refRaw', $v3);
        return new RawShapesOperationRequestBodyPayload(nullableRaw: $v2, refRaw: $v4);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeAbcJsonValue(mixed $value, string $path): Abc
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendPath($path, 'def');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'def', $v1), $v1);
        return new Abc(def: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeSchemaObjectPropertyJsonValue(mixed $value, string $path): SchemaObjectProperty
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'stringProperty', $v1), $v1);
        return new SchemaObjectProperty(stringProperty: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeSchemaObjectArrayPropertyJsonValue(mixed $value, string $path): SchemaObjectArrayProperty
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'stringProperty', $v1), $v1);
        return new SchemaObjectArrayProperty(stringProperty: $v2);
    }
    /**
     * @param list<Constraint> $constraints
     *
     * @throws ValidationException
     */
    public function validate(mixed $value, string $path, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (\count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = ['path' => $this->appendPath($path, (string) $violation->getPropertyPath()), 'code' => $this->getViolationCode($violation), 'message' => (string) $violation->getMessage()];
            }
            throw new ValidationException($errors);
        }
    }
    /**
     * Joins a value location to one of its sub locations, using the property path syntax the
     * Symfony validator already produces: a dot before a property, brackets around an index.
     */
    public function appendPath(string $base, string $sub): string
    {
        if ($sub === '') {
            return $base;
        }
        if ($base === '') {
            return $sub;
        }
        return str_starts_with($sub, '[') ? "{$base}{$sub}" : "{$base}.{$sub}";
    }
    /**
     * A machine readable code for a violation, so that a client does not have to match on the
     * English sentence.
     */
    public function getViolationCode(ConstraintViolationInterface $violation): string
    {
        $constraint = $violation instanceof ConstraintViolation ? $violation->getConstraint() : null;
        if ($constraint === null) {
            return 'invalid_value';
        }
        if (str_starts_with($constraint::class, 'Zol\Apifony\Tests\TestOpenApiServer\Format\\')) {
            return 'invalid_format';
        }
        return match ($constraint::class) {
            Assert\NotNull::class, Assert\NotBlank::class => 'required',
            Assert\Type::class => 'invalid_type',
            Assert\Length::class => 'invalid_length',
            Assert\Choice::class => 'invalid_enum_value',
            Assert\GreaterThan::class, Assert\GreaterThanOrEqual::class, Assert\LessThan::class, Assert\LessThanOrEqual::class, Assert\Range::class => 'out_of_range',
            Assert\DivisibleBy::class => 'invalid_multiple',
            Assert\Count::class => 'invalid_count',
            Assert\Unique::class => 'duplicate_values',
            Assert\Regex::class => 'invalid_pattern',
            default => 'invalid_value',
        };
    }
}