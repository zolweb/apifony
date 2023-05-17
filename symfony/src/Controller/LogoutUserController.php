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

class LogoutUserController extends AbstractController
{
    #[Route(
        path: '/user/logout',
        methods: ['get'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LogoutUserHandlerInterface $handler,
    ): Response {
        $errors = [];
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
