<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Locale;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
class LocaleController extends AbstractController
{
    private LocaleHandler $handler;
    public function setHandler(LocaleHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function getLocaleList(Request $request): Response
    {
        $errors = [];
    }
}