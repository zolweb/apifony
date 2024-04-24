<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Uuid as AssertUuid;
use Zol\Ogen\Tests\TestOpenApiServer\Model\ContentTranslation;
class ContentTranslationController extends AbstractController
{
    private ContentTranslationHandler $handler;
    public function setHandler(ContentTranslationHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function postContentTranslation(Request $request): Response
    {
        $errors = [];
    }
    public function patchContentTranslation(Request $request, string $contentTranslationId): Response
    {
        $errors = [];
        $pContentTranslationId = $contentTranslationId;
        try {
            $this->validateParameter($pContentTranslationId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTranslationId'] = $e->messages;
        }
    }
    public function getContentTranslation(Request $request, string $contentTranslationId): Response
    {
        $errors = [];
        $pContentTranslationId = $contentTranslationId;
        try {
            $this->validateParameter($pContentTranslationId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTranslationId'] = $e->messages;
        }
    }
    public function getContentTranslationDataVersions(Request $request, string $contentTranslationId): Response
    {
        $errors = [];
        $pContentTranslationId = $contentTranslationId;
        try {
            $this->validateParameter($pContentTranslationId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTranslationId'] = $e->messages;
        }
        $qPageNumber = '0';
        try {
            $qPageNumber = $this->getIntParameter($request, 'pageNumber', 'query', true);
            $this->validateParameter($qPageNumber, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['pageNumber'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['pageNumber'] = $e->messages;
        }
        $qPageSize = '0';
        try {
            $qPageSize = $this->getIntParameter($request, 'pageSize', 'query', true);
            $this->validateParameter($qPageSize, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['pageSize'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['pageSize'] = $e->messages;
        }
    }
    public function publishContentTranslation(Request $request, string $contentTranslationId): Response
    {
        $errors = [];
        $pContentTranslationId = $contentTranslationId;
        try {
            $this->validateParameter($pContentTranslationId, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTranslationId'] = $e->messages;
        }
    }
    public function withdrawContentTranslation(Request $request, string $contentTranslationId): Response
    {
        $errors = [];
        $pContentTranslationId = $contentTranslationId;
        try {
            $this->validateParameter($pContentTranslationId, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentTranslationId'] = $e->messages;
        }
    }
}