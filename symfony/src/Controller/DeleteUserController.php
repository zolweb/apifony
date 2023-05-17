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

class DeleteUserController extends AbstractController
{
    #[Route(
        path: '/user/{username}',
        requirements: [
            'username' => '[^:/?#[]@!$&\'()*+,;=]+',
        ],
        methods: ['delete'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DeleteUserHandlerInterface $handler,
        string $username,
    ): Response {
        $pUsername = $username;
        $errors = [];
        $violations = $validator->validate(
            $pUsername,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['username'] = array_map(
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
        $responsePayloadContentType = $request->headers->get('accept', 'unspecified');
        switch (true) {
            case is_null($requestBodyPayload):
                $responsePayload = match ($responsePayloadContentType) {
                    '' =>
                        $handler->handleEmptyPayloadToEmptyContent(
                            $pUsername,
                        ),
                    default => (object) [
                        'code' => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        'content' => [
                            'code' => 'unsupported_response_type',
                            'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                        ],
                    ],
                };

                break;
            default:
                throw new RuntimeException();
        }
    }
}
