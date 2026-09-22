<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
use Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation\FirstOperationQueryParamObject;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Node;
use Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation\FirstOperationQueryParamObjectNestedObjectProperty;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Schema;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a string.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a string.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be an integer.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be an integer.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be an integer.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be an integer.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a numeric.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a numeric.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a numeric.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a numeric.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a boolean.");
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a boolean.");
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a boolean.");
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a boolean.");
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
    public function denormalizeListParameter(mixed $value, string $path, string $in): array
    {
        if (!\is_array($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be an array.");
        }
        if (!array_is_list($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be a list.");
        }
        return $value;
    }
    /**
     * @return array<string, mixed>
     *
     * @throws DenormalizationException
     */
    public function denormalizeMapParameter(mixed $value, string $path, string $in): array
    {
        if (!\is_array($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be an object.");
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
    public function getRequiredParameterProperty(array $values, string $key, string $path, string $in): mixed
    {
        if (!\array_key_exists($key, $values)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' is required.");
        }
        return $values[$key];
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeStringParameter(mixed $value, string $path, string $in): string
    {
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be a string.");
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeIntParameter(mixed $value, string $path, string $in): int
    {
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be an integer.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be an integer.");
        }
        return (int) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFloatParameter(mixed $value, string $path, string $in): float
    {
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be a numeric.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be a numeric.");
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeBoolParameter(mixed $value, string $path, string $in): bool
    {
        if (!\is_string($value)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be a boolean.");
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '{$path}' in '{$in}' must be a boolean.");
        }
        return ['true' => true, 'false' => false][$value];
    }
    public function getParameterErrorMessage(string $path, string $in, string $expectation): string
    {
        return "Parameter '{$path}' in '{$in}' {$expectation}";
    }
    public function getJsonErrorMessage(string $path, string $expectation): string
    {
        return $path === '' ? "Request body {$expectation}" : "Property '{$path}' in 'requestBody' {$expectation}";
    }
    public function appendJsonPath(string $path, string $key): string
    {
        return $path === '' ? $key : "{$path}.{$key}";
    }
    /**
     * @throws DenormalizationException
     */
    public function getJsonRequestBody(Request $request): mixed
    {
        $value = $request->getContent();
        if ($value === '') {
            throw new DenormalizationException('Request body must not be null.');
        }
        $value = json_decode($value, true);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new DenormalizationException('Request body is not a valid JSON document.');
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
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be an array.'));
        }
        if (!array_is_list($value)) {
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be a list.'));
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
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be an object.'));
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
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'is required.'));
        }
        return $values[$key];
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeStringJson(mixed $value, string $path): string
    {
        if (!\is_string($value)) {
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be a string.'));
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeIntJson(mixed $value, string $path): int
    {
        if (!\is_int($value)) {
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be an integer.'));
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFloatJson(mixed $value, string $path): float
    {
        if (!\is_int($value) && !\is_float($value)) {
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be a numeric.'));
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeBoolJson(mixed $value, string $path): bool
    {
        if (!\is_bool($value)) {
            throw new DenormalizationException($this->getJsonErrorMessage($path, 'must be a boolean.'));
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringRequestBody(Request $request, ?string $default = null): string
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_string($value)) {
            throw new DenormalizationException('Request body must be a string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringOrNullRequestBody(Request $request, ?string $default = null): ?string
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_string($value)) {
            throw new DenormalizationException('Request body must be a string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntRequestBody(Request $request, ?int $default = null): int
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_int($value)) {
            throw new DenormalizationException('Request body must be an integer.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntOrNullRequestBody(Request $request, ?int $default = null): ?int
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if ($value === null) {
            return null;
        }
        if (!\is_int($value)) {
            throw new DenormalizationException('Request body must be an integer.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatRequestBody(Request $request, ?float $default = null): float
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_int($value) && !\is_float($value)) {
            throw new DenormalizationException('Request body must be a numeric.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatOrNullRequestBody(Request $request, ?float $default = null): ?float
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if ($value === null) {
            return null;
        }
        if (!\is_int($value) && !\is_float($value)) {
            throw new DenormalizationException('Request body must be a numeric.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolRequestBody(Request $request, ?bool $default = null): bool
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_bool($value)) {
            throw new DenormalizationException('Request body must be a boolean.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolOrNullRequestBody(Request $request, ?bool $default = null): ?bool
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if ($value === null) {
            return null;
        }
        if (!\is_bool($value)) {
            throw new DenormalizationException('Request body must be a boolean.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeAbcParameterValue(mixed $value, string $path, string $in): Abc
    {
        $v0 = $this->denormalizeMapParameter($value, $path, $in);
        $v1 = "{$path}[def]";
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'def', $v1, $in), $v1, $in);
        return new Abc(def: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeFirstOperationQueryParamObjectParameterValue(mixed $value, string $path, string $in): FirstOperationQueryParamObject
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
    public function denormalizeNodeParameterValue(mixed $value, string $path, string $in): Node
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
    public function denormalizeFirstOperationQueryParamObjectNestedObjectPropertyParameterValue(mixed $value, string $path, string $in): FirstOperationQueryParamObjectNestedObjectProperty
    {
        $v0 = $this->denormalizeMapParameter($value, $path, $in);
        $v1 = "{$path}[emailProperty]";
        $v2 = $this->denormalizeStringParameter($this->getRequiredParameterProperty($v0, 'emailProperty', $v1, $in), $v1, $in);
        return new FirstOperationQueryParamObjectNestedObjectProperty(emailProperty: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeSchemaJsonValue(mixed $value, string $path): Schema
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendJsonPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'stringProperty', $v1), $v1);
        $v3 = $this->appendJsonPath($path, 'numberProperty');
        $v4 = $this->denormalizeFloatJson($this->getRequiredJsonProperty($v0, 'numberProperty', $v3), $v3);
        $v5 = $this->appendJsonPath($path, 'integerProperty');
        $v6 = $this->denormalizeIntJson($this->getRequiredJsonProperty($v0, 'integerProperty', $v5), $v5);
        $v7 = $this->appendJsonPath($path, 'booleanProperty');
        $v8 = $this->denormalizeBoolJson($this->getRequiredJsonProperty($v0, 'booleanProperty', $v7), $v7);
        $v9 = $this->appendJsonPath($path, 'enumStringProperty');
        $v10 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'enumStringProperty', $v9), $v9);
        if (!\in_array($v10, ['abc', 'def', 'ghi'], true)) {
            throw new DenormalizationException($this->getJsonErrorMessage($v9, 'must be one of \'abc\', \'def\', \'ghi\'.'));
        }
        $v11 = $this->appendJsonPath($path, 'enumNullableStringProperty');
        $v13 = $this->getRequiredJsonProperty($v0, 'enumNullableStringProperty', $v11);
        $v12 = null;
        if ($v13 !== null) {
            $v12 = $this->denormalizeStringJson($v13, $v11);
            if (!\in_array($v12, ['abc', 'def', 'ghi', null], true)) {
                throw new DenormalizationException($this->getJsonErrorMessage($v11, 'must be one of \'abc\', \'def\', \'ghi\', null.'));
            }
        }
        $v14 = $this->appendJsonPath($path, 'integerRangeProperty');
        $v15 = $this->denormalizeIntJson($this->getRequiredJsonProperty($v0, 'integerRangeProperty', $v14), $v14);
        if ($v15 < -5 || $v15 > 5) {
            throw new DenormalizationException($this->getJsonErrorMessage($v14, 'must be between -5 and 5.'));
        }
        $v16 = $this->appendJsonPath($path, 'emailProperty');
        $v17 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'emailProperty', $v16), $v16);
        $v18 = $this->appendJsonPath($path, 'uuidProperty');
        $v19 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'uuidProperty', $v18), $v18);
        $v20 = $this->appendJsonPath($path, 'dateTimeProperty');
        $v21 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty', $v20), $v20);
        $v22 = $this->appendJsonPath($path, 'dateTimeProperty2');
        $v23 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty2', $v22), $v22);
        $v24 = $this->appendJsonPath($path, 'dateTimeProperty3');
        $v25 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty3', $v24), $v24);
        $v26 = $this->appendJsonPath($path, 'dateTimeProperty4');
        $v27 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateTimeProperty4', $v26), $v26);
        $v28 = $this->appendJsonPath($path, 'dateProperty');
        $v29 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'dateProperty', $v28), $v28);
        $v30 = $this->appendJsonPath($path, 'timeProperty');
        $v31 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty', $v30), $v30);
        $v32 = $this->appendJsonPath($path, 'timeProperty2');
        $v33 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty2', $v32), $v32);
        $v34 = $this->appendJsonPath($path, 'timeProperty3');
        $v35 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty3', $v34), $v34);
        $v36 = $this->appendJsonPath($path, 'timeProperty4');
        $v37 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'timeProperty4', $v36), $v36);
        $v38 = $this->appendJsonPath($path, 'customProperty');
        $v39 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'customProperty', $v38), $v38);
        $v41 = 'abc';
        if (\array_key_exists('defaultProperty', $v0)) {
            $v40 = $this->appendJsonPath($path, 'defaultProperty');
            $v41 = $this->denormalizeStringJson($v0['defaultProperty'], $v40);
        }
        $v43 = null;
        if (\array_key_exists('nullDefaultProperty', $v0)) {
            $v42 = $this->appendJsonPath($path, 'nullDefaultProperty');
            $v44 = $v0['nullDefaultProperty'];
            $v43 = null;
            if ($v44 !== null) {
                $v43 = $this->denormalizeStringJson($v44, $v42);
            }
        }
        $v46 = [];
        if (\array_key_exists('emptyArrayDefaultProperty', $v0)) {
            $v45 = $this->appendJsonPath($path, 'emptyArrayDefaultProperty');
            $v46 = [];
            foreach ($this->denormalizeListJson($v0['emptyArrayDefaultProperty'], $v45) as $v47 => $v48) {
                $v49 = "{$v45}[{$v47}]";
                $v50 = $this->denormalizeStringJson($v48, $v49);
                $v46[] = $v50;
            }
        }
        $v52 = 'def';
        if (\array_key_exists('overriddenProperty', $v0)) {
            $v51 = $this->appendJsonPath($path, 'overriddenProperty');
            $v52 = $this->denormalizeStringJson($v0['overriddenProperty'], $v51);
        }
        $v53 = $this->appendJsonPath($path, 'objectProperty');
        $v54 = $this->denormalizeSchemaObjectPropertyJsonValue($this->getRequiredJsonProperty($v0, 'objectProperty', $v53), $v53);
        $v55 = $this->appendJsonPath($path, 'arrayProperty');
        $v56 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'arrayProperty', $v55), $v55) as $v57 => $v58) {
            $v59 = "{$v55}[{$v57}]";
            $v60 = $this->denormalizeStringJson($v58, $v59);
            $v56[] = $v60;
        }
        $v61 = $this->appendJsonPath($path, 'integerMatrixProperty');
        $v62 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'integerMatrixProperty', $v61), $v61) as $v63 => $v64) {
            $v65 = "{$v61}[{$v63}]";
            $v66 = [];
            foreach ($this->denormalizeListJson($v64, $v65) as $v67 => $v68) {
                $v69 = "{$v65}[{$v67}]";
                $v70 = $this->denormalizeIntJson($v68, $v69);
                $v66[] = $v70;
            }
            $v62[] = $v66;
        }
        $v71 = $this->appendJsonPath($path, 'objectArrayProperty');
        $v72 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'objectArrayProperty', $v71), $v71) as $v73 => $v74) {
            $v75 = "{$v71}[{$v73}]";
            $v76 = $this->denormalizeSchemaObjectArrayPropertyJsonValue($v74, $v75);
            $v72[] = $v76;
        }
        $v77 = $this->appendJsonPath($path, 'recursiveObjectArray');
        $v78 = [];
        foreach ($this->denormalizeListJson($this->getRequiredJsonProperty($v0, 'recursiveObjectArray', $v77), $v77) as $v79 => $v80) {
            $v81 = "{$v77}[{$v79}]";
            $v82 = $this->denormalizeSchemaJsonValue($v80, $v81);
            $v78[] = $v82;
        }
        return new Schema(stringProperty: $v2, numberProperty: $v4, integerProperty: $v6, booleanProperty: $v8, enumStringProperty: $v10, enumNullableStringProperty: $v12, integerRangeProperty: $v15, emailProperty: $v17, uuidProperty: $v19, dateTimeProperty: $v21, dateTimeProperty2: $v23, dateTimeProperty3: $v25, dateTimeProperty4: $v27, dateProperty: $v29, timeProperty: $v31, timeProperty2: $v33, timeProperty3: $v35, timeProperty4: $v37, customProperty: $v39, defaultProperty: $v41, nullDefaultProperty: $v43, emptyArrayDefaultProperty: $v46, overriddenProperty: $v52, objectProperty: $v54, arrayProperty: $v56, integerMatrixProperty: $v62, objectArrayProperty: $v72, recursiveObjectArray: $v78);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeSchemaObjectPropertyJsonValue(mixed $value, string $path): SchemaObjectProperty
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendJsonPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'stringProperty', $v1), $v1);
        return new SchemaObjectProperty(stringProperty: $v2);
    }
    /**
     * @throws DenormalizationException
     */
    public function denormalizeSchemaObjectArrayPropertyJsonValue(mixed $value, string $path): SchemaObjectArrayProperty
    {
        $v0 = $this->denormalizeMapJson($value, $path);
        $v1 = $this->appendJsonPath($path, 'stringProperty');
        $v2 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'stringProperty', $v1), $v1);
        return new SchemaObjectArrayProperty(stringProperty: $v2);
    }
    /**
     * @param list<Constraint> $constraints
     *
     * @throws ParameterValidationException
     */
    public function validateParameter(mixed $value, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (\count($violations) > 0) {
            throw new ParameterValidationException(array_map(static fn(ConstraintViolationInterface $violation) => $violation->getPropertyPath() === '' ? (string) $violation->getMessage() : "{$violation->getPropertyPath()}: {$violation->getMessage()}", iterator_to_array($violations)));
        }
    }
    /**
     * @param list<Constraint> $constraints
     *
     * @throws RequestBodyValidationException
     */
    public function validateRequestBody(mixed $value, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (\count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $path = $violation->getPropertyPath();
                if (!isset($errors[$path])) {
                    $errors[$path] = [];
                }
                $errors[$path][] = (string) $violation->getMessage();
            }
            throw new RequestBodyValidationException($errors);
        }
    }
}