<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

use Zol\Ogen\Tests\TestOpenApiServer\Api\DenormalizationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\ParameterValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\RequestBodyValidationException;
use Zol\Ogen\Tests\TestOpenApiServer\Api\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
class AtomController extends AbstractController
{
    private AtomHandler $handler;
    public function setHandler(AtomHandler $handler): void
    {
        $this->handler = $handler;
    }
    public function getAtom(Request $request, string $atomId): Response
    {
        $errors = [];
        $pAtomId = $atomId;
        try {
            $this->validateParameter($pAtomId, [new Assert\NotNull()]);
        } catch (ParameterValidationException $e) {
            $errors['path']['atomId'] = $e->messages;
        }
    }
    public function postAtom(Request $request): Response
    {
        $errors = [];
    }
}