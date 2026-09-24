<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentBucketOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
class ComponentBucketOperationController extends AbstractController
{
    private ComponentBucketOperationHandler $handler;
    public function setHandler(ComponentBucketOperationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function componentBucketOperation(Request $request, string $bucketPathParam): Response
    {
        $errors = [];
        $pBucketPathParam = $bucketPathParam;
        $qBucketQueryParam = (new \ReflectionClass(Abc::class))->newInstanceWithoutConstructor();
        try {
            $qBucketQueryParam = $this->denormalizeQBucketQueryParamParameter($request, 'bucketQueryParam', 'query');
            $this->validate($qBucketQueryParam, 'bucketQueryParam', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        $requestBodyPayload = (new \ReflectionClass(Abc::class))->newInstanceWithoutConstructor();
        try {
            $requestBodyPayload = $this->denormalizeAbcJsonValue($this->getJsonRequestBody($request), '');
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
        $response = $this->handler->componentBucketOperation($pBucketPathParam, $qBucketQueryParam, $requestBodyPayload);
        if ($response->getContentType() === 'application/json') {
            return new JsonResponse($response->payload, $response->getCode(), $response->getHeaders());
        }
        return new Response('', $response->getCode(), $response->getHeaders());
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQBucketQueryParamParameter(Request $request, string $name, string $in): Abc
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        return $this->denormalizeAbcParameterValue($this->getRawParameter($request, $name, $in), $name);
    }
}