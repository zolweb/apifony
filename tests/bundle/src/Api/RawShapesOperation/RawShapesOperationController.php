<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
class RawShapesOperationController extends AbstractController
{
    private RawShapesOperationHandler $handler;
    public function setHandler(RawShapesOperationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function rawShapesOperation(Request $request): Response
    {
        $errors = [];
        $qTypelessParam = null;
        try {
            $qTypelessParam = $this->denormalizeQTypelessParamParameter($request, 'typelessParam', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qStringParam = null;
        try {
            $qStringParam = $this->denormalizeQStringParamParameter($request, 'stringParam', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $requestBodyPayload = (new \ReflectionClass(RawShapesOperationRequestBodyPayload::class))->newInstanceWithoutConstructor();
        try {
            $requestBodyPayload = $this->denormalizeRawShapesOperationRequestBodyPayloadJsonValue($this->getJsonRequestBody($request), '');
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
        $response = $this->handler->rawShapesOperation($qTypelessParam, $qStringParam, $requestBodyPayload);
        if ($response->getContentType() === 'application/json') {
            return new JsonResponse($response->payload, $response->getCode(), $response->getHeaders());
        }
        return new Response('', $response->getCode(), $response->getHeaders());
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQTypelessParamParameter(Request $request, string $name, string $in): mixed
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        return $this->getRawParameter($request, $name, $in);
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQStringParamParameter(Request $request, string $name, string $in): mixed
    {
        if (!$this->hasParameter($request, $name, $in)) {
            return 'fallback';
        }
        return $this->getRawParameter($request, $name, $in);
    }
}