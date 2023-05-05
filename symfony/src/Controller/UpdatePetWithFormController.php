<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdatePetWithFormController extends AbstractController
{
    #[Route(
        path: '/pet/{petId}',
        requirements: [
            'pPetId' => '-?(0|[1-9]\d*)',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UpdatePetWithFormHandlerInterface $handler,
        int $pPetId,
    ): Response {
        $qName = ($request->query->get('name', null));
        $qStatus = ($request->query->get('status', null));
        $errors = [];
        $violations = $validator->validate(
            $pPetId,
            [
                new Assert\NotNull(),
                new Int64(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pPetId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qName,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qName'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qStatus,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qStatus'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }
        return $handler->handle(
            $qName,
            $pPetId,
            $qStatus,
        );
    }
}