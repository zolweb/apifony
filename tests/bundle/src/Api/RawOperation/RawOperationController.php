<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
class RawOperationController extends AbstractController
{
    private RawOperationHandler $handler;
    public function setHandler(RawOperationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function rawOperation(Request $request): Response
    {
        $queryErrors = [];
        $requestBodyErrors = [];
        $qRawParam = null;
        try {
            $qRawParam = $this->denormalizeQRawParamParameter($request, 'rawParam', 'query');
            $this->validateParameter($qRawParam, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $queryErrors['rawParam'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $queryErrors['rawParam'] = $e->messages;
        }
        $requestBodyPayload = null;
        try {
            $requestBodyPayload = $this->getJsonRequestBody($request);
            $this->validateRequestBody($requestBodyPayload, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $requestBodyErrors = [$e->getMessage()];
        } catch (RequestBodyValidationException $e) {
            $requestBodyErrors = $e->messages;
        }
        $errors = [];
        if (\count($queryErrors) > 0) {
            $errors['query'] = $queryErrors;
        }
        if (\count($requestBodyErrors) > 0) {
            $errors['requestBody'] = $requestBodyErrors;
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
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
        }
        return $this->getRawParameter($request, $name, $in);
    }
}