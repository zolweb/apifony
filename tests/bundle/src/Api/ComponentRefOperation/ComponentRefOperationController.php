<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentRefOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\ValidationException;
use Zol\Apifony\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
class ComponentRefOperationController extends AbstractController
{
    private ComponentRefOperationHandler $handler;
    public function setHandler(ComponentRefOperationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function componentRefOperation(Request $request): Response
    {
        $errors = [];
        $qStringListParam = [];
        try {
            $qStringListParam = $this->denormalizeQStringListParamParameter($request, 'stringListParam', 'query');
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        }
        $qAbcListParam = [];
        try {
            $qAbcListParam = $this->denormalizeQAbcListParamParameter($request, 'abcListParam', 'query');
            $this->validate($qAbcListParam, 'abcListParam', [new Assert\Valid()]);
        } catch (DenormalizationException $e) {
            $errors[] = ['in' => 'query', 'path' => $e->path, 'code' => $e->errorCode, 'message' => $e->getMessage()];
        } catch (ValidationException $e) {
            foreach ($e->errors as $error) {
                $errors[] = ['in' => 'query', ...$error];
            }
        }
        if (\count($errors) > 0) {
            return new JsonResponse(['code' => 'validation_failed', 'message' => 'Validation has failed.', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $response = $this->handler->componentRefOperation($qStringListParam, $qAbcListParam);
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
    private function denormalizeQStringListParamParameter(Request $request, string $name, string $in): array
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
     * @return list<Abc>
     *
     * @throws DenormalizationException
     */
    private function denormalizeQAbcListParamParameter(Request $request, string $name, string $in): array
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
}