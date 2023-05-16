<?php

namespace App\Controller;

use RuntimeException;
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

class GetPetByIdController extends AbstractController
{
    #[Route(
        path: '/pet/{petId}',
        requirements: [
            'petId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['get'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        GetPetByIdHandlerInterface $handler,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $errors = [];
        $violations = $validator->validate(
            $pPetId,
            [
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
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
        switch (true) {
            case is_null($content):
                return match ($request->headers->get('accept', 'unspecified')) {
                    'ApplicationJson' =>
                        $handler->handleNullApplicationJson(
                            $pPetId,
                        ),
                    default =>
                        new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$contentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        ),
                };
            default:
                throw new RuntimeException();
        }
    }
}
