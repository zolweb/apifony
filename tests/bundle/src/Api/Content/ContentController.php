<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\Uuid as AssertUuid;
use Zol\Ogen\Tests\TestOpenApiServer\Model\Content;
class ContentController extends AbstractController
{
    private ContentHandler $handler;
    public function setHandler(ContentHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function postContent(Request $request): Response
    {
        $errors = [];
    }
    public function getContentList(Request $request): Response
    {
        $errors = [];
        $qContentTypeId = '';
        try {
            $qContentTypeId = $this->getStringOrNullParameter($request, 'contentTypeId', 'query', false);
            $this->validateParameter($qContentTypeId, []);
        } catch (DenormalizationException $e) {
            $errors['query']['contentTypeId'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['contentTypeId'] = $e->messages;
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
        $qSort = '';
        try {
            $qSort = $this->getStringOrNullParameter($request, 'sort', 'query', false);
            $this->validateParameter($qSort, [new Assert\Regex(pattern: '/^(creationTimestamp|name)(:(asc|desc))?$/')]);
        } catch (DenormalizationException $e) {
            $errors['query']['sort'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['sort'] = $e->messages;
        }
        $qStatus = '';
        try {
            $qStatus = $this->getStringOrNullParameter($request, 'status', 'query', false);
            $this->validateParameter($qStatus, []);
        } catch (DenormalizationException $e) {
            $errors['query']['status'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['status'] = $e->messages;
        }
        $qSearch = '';
        try {
            $qSearch = $this->getStringOrNullParameter($request, 'search', 'query', false);
            $this->validateParameter($qSearch, []);
        } catch (DenormalizationException $e) {
            $errors['query']['search'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['search'] = $e->messages;
        }
    }
    public function getContent(Request $request, string $contentId): Response
    {
        $errors = [];
        $pContentId = $contentId;
        try {
            $this->validateParameter($pContentId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }
    }
    public function patchContent(Request $request, string $contentId): Response
    {
        $errors = [];
        $pContentId = $contentId;
        try {
            $this->validateParameter($pContentId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }
    }
    public function archiveContent(Request $request, string $contentId): Response
    {
        $errors = [];
        $pContentId = $contentId;
        try {
            $this->validateParameter($pContentId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }
    }
    public function restoreContent(Request $request, string $contentId): Response
    {
        $errors = [];
        $pContentId = $contentId;
        try {
            $this->validateParameter($pContentId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }
    }
    public function duplicateContent(Request $request, string $contentId): Response
    {
        $errors = [];
        $pContentId = $contentId;
        try {
            $this->validateParameter($pContentId, [new Assert\NotNull(), new AssertUuid()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['contentId'] = $e->messages;
        }
    }
}