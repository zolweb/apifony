<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\MediaTree;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Zol\Ogen\Tests\TestOpenApiServer\Format\MediaFolderId as AssertMediaFolderId;
use Zol\Ogen\Tests\TestOpenApiServer\Model\MediaTree;
use Zol\Ogen\Tests\TestOpenApiServer\Model\MediaFolder;
class MediaTreeController extends AbstractController
{
    private MediaTreeHandler $handler;
    public function setHandler(MediaTreeHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function getMediaTree(Request $request): Response
    {
        $errors = [];
        $qPath = '';
        try {
            $qPath = $this->getStringParameter($request, 'path', 'query', false, '\'root\'');
            $this->validateParameter($qPath, [new Assert\NotNull()]);
        } catch (DenormalizationException $e) {
            $errors['query']['path'] = [$e->messages];
        } catch (ParameterValidationException $e) {
            $errors['query']['path'] = $e->messages;
        }
    }
    public function moveToMediaFolder(Request $request): Response
    {
        $errors = [];
    }
    public function addMediaFolder(Request $request): Response
    {
        $errors = [];
    }
    public function getMediaFolder(Request $request, string $mediaFolderId): Response
    {
        $errors = [];
        $pMediaFolderId = $mediaFolderId;
        try {
            $this->validateParameter($pMediaFolderId, [new Assert\NotNull(), new AssertMediaFolderId()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['mediaFolderId'] = $e->messages;
        }
    }
    public function updateMediaFolder(Request $request, string $mediaFolderId): Response
    {
        $errors = [];
        $pMediaFolderId = $mediaFolderId;
        try {
            $this->validateParameter($pMediaFolderId, [new Assert\NotNull(), new AssertMediaFolderId()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['mediaFolderId'] = $e->messages;
        }
    }
    public function deleteMediaFolder(Request $request, string $mediaFolderId): Response
    {
        $errors = [];
        $pMediaFolderId = $mediaFolderId;
        try {
            $this->validateParameter($pMediaFolderId, [new Assert\NotNull(), new AssertMediaFolderId()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['mediaFolderId'] = $e->messages;
        }
    }
}