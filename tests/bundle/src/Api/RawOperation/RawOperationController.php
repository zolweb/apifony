<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class RawOperationController extends AbstractController
{
    private RawOperationHandler $handler;
    public function setHandler(RawOperationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function rawOperation(Request $request): Response
    {
        $errors = [];
        $qRawParam = null;
        try {
            $qRawParam = $this->denormalizeQRawParamParameter($request, 'rawParam', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $requestBodyPayload = null;
        try {
            $requestBodyPayload = $this->getJsonRequestBody($request);
            $this->validate($requestBodyPayload, '', []);
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
        $response = $this->handler->rawOperation($qRawParam, $requestBodyPayload);
        if ($response->getContentType() === 'application/json') {
            return new JsonResponse($response->payload, $response->getCode(), $response->getHeaders());
        }
        return new Response('', $response->getCode(), $response->getHeaders());
    }
    /**
     * @throws DenormalizationException
     */
    private function denormalizeQRawParamParameter(Request $request, string $name, string $in): mixed
    {
        if (!$this->hasParameter($request, $name, $in)) {
            throw new DenormalizationException($name, 'required', 'This value is required.');
        }
        return $this->getRawParameter($request, $name, $in);
    }
}